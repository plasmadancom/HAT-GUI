<?php

/*
 * functions.php - Functions for HAT-GUI
 * 
 * Copyright (C) 2020 Dan Jones - https://github.com/plasmadancom
 * 
 * 
 * -----------------------------------------------------------------------------
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * -----------------------------------------------------------------------------
 */

// Detect device
function detect_i2c($i2c_addr) {
    // We'll use i2c-tools for this
    exec("i2cget -y 1 $i2c_addr", $result);
    
    return (bool)$result[0];
}

// Initialise GPIO
function init_gpio($gpio, $mode, $extension, $pinBase, $i2c_addr, $spoof = false) {
    if ($spoof !== false) return;
    
    $pin = $pinBase + $gpio;
    
    // Set pin mode
    exec("gpio -x $extension:$pinBase:$i2c_addr mode $pin $mode");
}

// Return GPIO status
function gpio($gpio, $extension, $pinBase, $i2c_addr, $spoof = false) {
    if ($spoof !== false) return 0;
    
    $num = $pinBase + $gpio;
    
    // Read pin status
    exec("gpio -x $extension:$pinBase:$i2c_addr read $num", $result);
    
    return (int)$result[0];
}

// Toggle GPIO state & return
function gpio_toggle($gpio, $extension, $pinBase, $i2c_addr) {
    $num = $pinBase + $gpio;
    
    // Set pin mode
    // Done at page load
    // exec("gpio -x $extension:$pinBase:$i2c_addr mode $num out");
    
    // Toggle pin output
    exec("gpio -x $extension:$pinBase:$i2c_addr toggle $num");
    
    // Return new status
    return gpio($gpio, $extension, $pinBase, $i2c_addr);
}

// Write GPIO & return
function gpio_write($gpio, $write, $extension, $pinBase, $i2c_addr) {
    $num = $pinBase + $gpio;
    
    // Set pin mode
    // Done at page load
    // exec("gpio -x $extension:$pinBase:$i2c_addr mode $num out");
    
    // Write output
    exec("gpio -x $extension:$pinBase:$i2c_addr write $num $write");
    
    // Return new status
    return gpio($gpio, $extension, $pinBase, $i2c_addr);
}

// Write All GPIOs
// FUTURE USE: Not quite got this one figured out yet, but will be far better than using a loop

function write_gpios($write, $i2c_addr) {
    // TODO: Set the output dynamically for initialised pins only
    $out = $write ? '0xff' : '0x00';
    
    // Write the entire bank at once
    // TODO: Write both banks together?
    exec("sudo i2cset -y 1 $i2c_addr 0x14 0xff"); // Bank A
    exec("sudo i2cset -y 1 $i2c_addr 0x13 0xff"); // Bank B
}

// AJAX requests
if (isset($_POST) && !empty($_POST)) {
    // Pass additional params, or failover
    $extension = (isset($_POST['extension']) && !empty($_POST['extension'])) ? $_POST['extension'] : 'mcp23017';
    $pinBase = (isset($_POST['pinbase']) && is_numeric($_POST['pinbase'])) ? $_POST['pinbase'] : 100;
    $i2c_addr = (isset($_POST['i2c']) && !empty($_POST['i2c'])) ? $_POST['i2c'] : 0x20;
    
    $result = array();
    
    // Toggle GPIO
    if (isset($_POST['toggle']) && is_numeric($_POST['toggle'])) {
        $result[] = gpio_toggle($_POST['toggle'], $extension, $pinBase, $i2c_addr);
    }
    
    // Write GPIOs
    else if (isset($_POST['write']) && isset($_POST['gpios']) && is_array($_POST['gpios'])) {
        $write = (bool)$_POST['write'] ? 1 : 0;
        
        //write_gpios($write, $i2c_addr);
        
        foreach ($_POST['gpios'] as $gpio) {
            $result[$gpio] = gpio_write($gpio, $write, $extension, $pinBase, $i2c_addr);
        }
    }
    
    // Return result
    if (!empty($result)) echo json_encode($result);
    
    exit();
}

// Re-define our global variables
// Allows us to include other board.php files without clashes

$board = new stdClass();
$board->board_name = $board_name;
$board->page_title = $page_title;
$board->description = !empty($description) ? $description : '';
$board->image_url = !empty($image_url) ? $image_url : '';
$board->github = !empty($github) ? $github : '';
$board->buy = !empty($buy) ? $buy : '';
$board->guide = !empty($guide) ? $guide : '';
$board->intro = !empty($intro) ? $intro : '';
$board->extension = $extension;

$i2c_addr = $default_i2c_addr;

// Return class for current page
function cur_page($loc) {
    return $loc == basename(strtok($_SERVER["REQUEST_URI"], '?')) ? ' class="active"' : '';
}

// I2C Selection
if (isset($_GET) && !empty($_GET)) {
    if (isset($_GET['i2c']) && !empty($_GET['i2c'])) {
        
        // Validate & override default address
        if (preg_match("/^0x2[0-7]$/i", $_GET['i2c'])) {
            $i2c_addr = hexdec($_GET['i2c']);
        }
    }
}

// Convert default I2C address to binary: A0: 1's, A1: 10's, A2: 100's
function i2c2bin($i) {
    return str_pad(decbin($i - 32), 3, 0, STR_PAD_LEFT);
}

// Kinda crazy method, but it's fast and it works!
$i2c_arr = array_reverse(str_split(i2c2bin($i2c_addr)));

$parsed_pins = array();
$css_classes = array();

// Parse board.php and construct page data
foreach ($pins as $pin => $i) {
    $p = array();
    
    // Arrays are 0 based but we want to start at pin 1
    ++$pin;
    
    $logic = false;
    $led = '';
    
    // Construct data which we can update / refer to later
    $p['name'] = array_key_exists(0, $i) ? $i[0] : '';
    $p['shortname'] = array_key_exists(1, $i) ? ' ' . $i[1] : '';
    $p['data'] = '';
    $p['guide'] = '';
    $p['css'] = '';
    
    // Add GPIO data
    if (array_key_exists('gpio', $i)) {
        $gpio = $i['gpio'];
        $mode = false;
        
        // Set mode
        if (array_key_exists('mode', $i)) {
            $mode = $i['mode'];
            $p['data'] .= ' data-mode="' . $mode . '"';
        }
        
        // Set default mode
        else if($default_pinMode !== false) {
            $mode = $default_pinMode;
            $p['data'] .= ' data-mode="' . $default_pinMode . '"';
        }
        
        else $p['data'] .= ' data-mode="unconfigured"';
        
        // Initialise GPIO with mode
        if($mode !== false) init_gpio($gpio, $mode, $extension, $pinBase, $i2c_addr);
        
        // Read GPIO status
        $logic = gpio($gpio, $extension, $pinBase, $i2c_addr, $demo_mode);
        $led = (bool)$logic !== false ? ' on' : '';
        
        $p['data'] .=  ' data-gpio="' . $gpio . '" data-logic="' . $logic . '"';
        $p['css'] .= ' gpio' . $gpio . $led;
    }
    
    // Add guide info
    if (array_key_exists('guide', $i)) {
        $p['data'] .= ' data-guide="' . $i['guide'] . '"';
        $p['guide'] .= ' ' . $i['guide'];
        
        // We can use this to match pin jumpers too
        $p['css'] .= ' ' . $i['guide'];
        
        // I2C address selection
        if (strpos($i['guide'], 'a0') or strpos($i['guide'], 'a1') or strpos($i['guide'], 'a2')) {
            $p['data'] .= ' data-mode="i2c"';
            
            // Default address
            if ((strpos($i['guide'], 'a0') and $i2c_arr[0] == 1) or (strpos($i['guide'], 'a1') and $i2c_arr[1] == 1) or (strpos($i['guide'], 'a2') and $i2c_arr[2] == 1)) $p['css'] .= ' pullup';
        }
    }
    
    // Add any additional CSS
    if (array_key_exists('css', $i)) {
        $p['css'] .= ' ' . $i['css'];
        
        // Split multiple classes
        $arr = explode(' ', $i['css']);
        
        // Process each class
        foreach ($arr as $css) {
            // Interactive class overlays
            $dynamic = strpos($css, 'led') !== false ? ' dynamic-led' . $led : ' dynamic-overlay';
            
            $css_classes[] = array('pin' => $pin, 'css' => ' ' . $css . $dynamic);
        }
    }
    
    $parsed_pins[$pin] = $p;
}

?>
