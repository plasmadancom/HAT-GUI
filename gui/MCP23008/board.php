<?php

/*
 * board.php - Pinout Config for HAT-GUI: MCP23008
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
$board_name = 'MCP23008';
$page_title = 'MCP23008 - Interactive UI &amp; Pinout Guide';
$description = 'The MCP23008 is an alternative to the MCP23017 with 8 GPIOs instead of 16. Since it works the same way and is supported in WiringPi we\'ve added it here.';
$image_url = 'https://io.plasmadan.com/img/hat-gui-mcp23008.gif';
$github = 'https://github.com/plasmadancom/HAT-GUI';

// Expander IC this board is based.
$extension = 'mcp23008';

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
// 'guide' => 'guide class'   /   'css' => 'css classes'

$pins = array (
    ['SCL (I&sup2;C CLOCK)',                  'SCL'],
    ['SDA (I&sup2;C DATA)',                   'SDA'],
    ['A2 I&sup2;C Address Selection)',        'A2',         'guide' => 'i2c-a2'],
    ['A1 I&sup2;C Address Selection)',        'A1',         'guide' => 'i2c-a1'],
    ['A0 I&sup2;C Address Selection)',        'A0',         'guide' => 'i2c-a0'],
    ['Reset',                                 'RESET',      'guide' => 'reset'],
    ['NC',                                    'NC'],
    ['Interupt',                              'INT',        'guide' => 'interrupt'],
    ['VSS',                                   'VSS',        'guide' => 'vss'],
    ['VDD',                                   'VDD',        'guide' => 'vdd'],
    ['GP 7',                                  'GP 7',       'gpio' => 7],
    ['GP 6',                                  'GP 6',       'gpio' => 6],
    ['GP 5',                                  'GP 5',       'gpio' => 5],
    ['GP 4',                                  'GP 4',       'gpio' => 4],
    ['GP 3',                                  'GP 3',       'gpio' => 3],
    ['GP 2',                                  'GP 2',       'gpio' => 2],
    ['GP 1',                                  'GP 1',       'gpio' => 1],
    ['GP 0',                                  'GP 0',       'gpio' => 0]
);


// Guides listed in pin setup above are defined here as html blocks.
// This will be displayed when the associated pin is selected.
// Guides can be used for multiple pins by chaining the classes together.
// Some default guides are included on index.php.
/*
ob_start(); ?>



<?php

$guide = ob_get_clean();
*/
// Intro
// Any relevant information about the board

ob_start(); ?>
                    <p>The MCP23008 is an alternative to the MCP23017 with 8 GPIOs instead of 16. Since it works the same way and is supported in WiringPi we've added it here.</p>
                    <p>Some of our future boards are based on the QFN package variant of the MCP23008 due its tiny footprint.</p>
<?php $intro = ob_get_clean(); ?>