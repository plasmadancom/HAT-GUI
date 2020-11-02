<?php

/*
 * board.php - Pinout Config for HAT-GUI: Appliance HAT
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


// Board information
$board_name = 'Appliance HAT';
$page_title = 'Appliance HAT - Interactive UI &amp; Pinout Guide';
$description = 'A Raspberry Pi HAT I/O board with quick connect power relays, ideally suited to appliance automation, IoT or industrial control applications.';
$image_url = 'https://io.plasmadan.com/img/ctrl-hat-gui.gif';
$github = 'https://github.com/plasmadancom/Appliance-HAT';
$buy = 'https://plasmadan.com/appliancehat';

// Expander IC this board is based.
$extension = 'mcp23017';

// Default I2C Address (0x20, 0x21, 0x22, 0x23, 0x24, 0x25, 0x26, 0x27).
$default_i2c_addr = 0x20;

// Default pinMode (out, up, in, false).
// Set to false to skip initialisation (if gpios used with other scripts).
// You can also set each pinMode individually below.
$default_pinMode = 'out';

// Setup all pin names, gpios, modes and other relevant info.
// Names and shortnames should be at keys 0 and 1 respectively.
//
// Additional options can be used to pass pin data or css classes.
// Image overlays can be used to display an indicator led, relay or other device.
// Each ccs class will be parsed and displayed as a div block with that class.
// These can be styled in CSS and targetted in Javascript to create interactive I/Os.
// CSS classes that contain "led" are dynamically displayed according to gpio status.
// Guide classes that contain "a0 / a1 / a2" are for I2C address selection.

// Required:
// Name  /  Short Name   /  'gpio' => 'gpio number'   /   'mode' => 'in / up / out' (if applicable)

// Optional:
// 'guide' => 'class'   /   'css' => 'css classes'

$pins = array (
    ['GPB 0',                                     'GPB 0',                         'gpio' => 8],
    ['GPB 1',                                     'GPB 1',                         'gpio' => 9],
    ['GPB 2',                                     'GPB 2',                         'gpio' => 10],
    ['GPB 3',                                     'GPB 3',                         'gpio' => 11],
    ['GPB 4',                                     'GPB 4',                         'gpio' => 12],
    ['GPB 5',                                     'GPB 5',                         'gpio' => 13],
    ['GPB 6',                                     'GPB 6',                         'gpio' => 14],
    ['GPB 7',                                     'GPB 7',                         'gpio' => 15],
    ['GPA 0 (Relay CH0)',                         'GPA 0 <small>(CH0)</small>',    'gpio' => 0,     'mode' => 'out',     'css' => 'relay0 led0'],
    ['GPA 1 (Relay CH1)',                         'GPA 1 <small>(CH1)</small>',    'gpio' => 1,     'mode' => 'out',     'css' => 'relay1 led1'],
    ['GPA 2 (Relay CH2)',                         'GPA 2 <small>(CH2)</small>',    'gpio' => 2,     'mode' => 'out',     'css' => 'relay2 led2'],
    ['GPA 3 (Relay CH3)',                         'GPA 3 <small>(CH3)</small>',    'gpio' => 3,     'mode' => 'out',     'css' => 'relay3 led3'],
    ['GPA 4 (Relay CH4)',                         'GPA 4 <small>(CH4)</small>',    'gpio' => 4,     'mode' => 'out',     'css' => 'relay4 led4'],
    ['GPA 5 (Relay CH5)',                         'GPA 5 <small>(CH5)</small>',    'gpio' => 5,     'mode' => 'out',     'css' => 'relay5 led5'],
    ['GPA 6',                                     'GPA 6',                         'gpio' => 6],
    ['GPA 7',                                     'GPA 7',                         'gpio' => 7],
    ['Interrupt A',                               'IA',                            'guide' => 'interrupt-a'],
    ['Interrupt B',                               'IB',                            'guide' => 'interrupt-b'],
    ['SDA (I&sup2;C DATA)',                       'SDA'],
    ['SCL (I&sup2;C CLOCK)',                      'SCL'],
    ['A0 (I&sup2;C Address Selection Jumper)',    'A0',                            'guide' => 'i2c-a0'],
    ['A0 (I&sup2;C Address Selection Jumper)',                                     'guide' => 'i2c-a0'],
    ['A1 (I&sup2;C Address Selection Jumper)',    'A1',                            'guide' => 'i2c-a1'],
    ['A1 (I&sup2;C Address Selection Jumper)',                                     'guide' => 'i2c-a1'],
    ['A2 (I&sup2;C Address Selection Jumper)',    'A2',                            'guide' => 'i2c-a2'],
    ['A2 (I&sup2;C Address Selection Jumper)',                                     'guide' => 'i2c-a2'],
    ['5V (5V GPIO Selection Jumper)',             '5V',                            'guide' => '5v'],
    ['5V (5V GPIO Selection Jumper)',                                              'guide' => '5v'],
    ['3V3 (3.3V GPIO Selection Jumper)',          '3V3',                           'guide' => '3v3'],
    ['3V3 (3.3V GPIO Selection Jumper)',                                           'guide' => '3v3'],
    ['LINK (Relay Power Jumper)',                 'LINK',                          'guide' => 'link'],
    ['LINK (Relay Power Jumper)',                                                  'guide' => 'link']
);

// Guides listed in pin setup above are defined here as html blocks.
// This will be displayed when the associated pin is selected.
// Guides can be used for multiple pins by chaining the classes together.
// Some default guides are included on index.php.

ob_start(); ?>

                    <!-- Link jumper -->
                    <div class="guide link">
                        <h3>Isolating the Relays</h3>
                        <p>Removing the LINK jumper will disconnect 5V power to the relays. This allows you to power the relays from a dedicated supply if required.</p>
                    </div>
                    
                    <!-- 3v3 jumper -->
                    <div class="guide 3v3">
                        <h3>GPIO Voltage</h3>
                        <p>The 3V3 Jumper is used to drive the GPIO expander voltage to 3V3.</p>
                        <p>To use with Arduino or any other 5V device the 3V3 jumper must be moved to 5V.</p>
                    </div>
                    
                    <!-- 5v jumper -->
                    <div class="guide 5v">
                        <h3>GPIO Voltage</h3>
                        <p>The 5V Jumper is used to drive the GPIO expander voltage to 5V.</p>
                        <p>Select this jumper to use with Arduino or any other 5V device and use the SDA &amp; SDL breakout pins for I2C communication.</p>
                    </div>
                
<?php

$guide = ob_get_clean();

// Intro
// Any relevant information about the board

ob_start(); ?>
                    <p>I/O board with quick connect power relays. Designed for switching high power loads such as appliances. Ideal for automation or industrial control applications.</p>
                    <p>Does not require any screw terminals or PCB wire connections. Instead it uses quick connect power relays with integrated spade terminals, meaning we have more space for relays.</p>
                    
                    <h2>Features</h2>
                    <ul>
                        <li>6 opto-isolated quick connect power relays</li>
                        <li>Up to 16A @ 250V</li>
                        <li>Based on the MCP23017 16-port GPIO expander</li>
                        <li>Jumper selectable I2C address &amp; GPIO voltage (3.3V / 5V)</li>
                        <li>Can be used with 3.3V or 5V I2C host devices (eg, Arduino)</li>
                        <li>Built-in user programmable ID EEPROM</li>
                    </ul>
<?php $intro = ob_get_clean(); ?>