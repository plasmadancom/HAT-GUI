<?php

/*
 * board.php - Pinout Config for HAT-GUI: MCP23017
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
$board_name = 'HAT-GUI !';
$page_title = 'MCP23017 &amp; MCP23008 - Interactive UI &amp; Pinout Guide';
$description = 'Pinout guide & interactive web GUI for MCP23017 and MCP23008 based Raspberry Pi expansion boards.';
$image_url = 'https://io.plasmadan.com/img/hat-gui.gif';
$github = 'https://github.com/plasmadancom/HAT-GUI';

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
// 'guide' => 'guide class'   /   'css' => 'css classes'

$pins = array (
    ['GPB 0',                                 'GPB 0',          'gpio' => 8],
    ['GPB 1',                                 'GPB 1',          'gpio' => 9],
    ['GPB 2',                                 'GPB 2',          'gpio' => 10],
    ['GPB 3',                                 'GPB 3',          'gpio' => 11],
    ['GPB 4',                                 'GPB 4',          'gpio' => 12],
    ['GPB 5',                                 'GPB 5',          'gpio' => 13],
    ['GPB 6',                                 'GPB 6',          'gpio' => 14],
    ['GPB 7',                                 'GPB 7',          'gpio' => 15],
    ['VDD - Power',                           'VDD',            'guide' => 'vdd'],
    ['VSS - Ground',                          'VSS',            'guide' => 'vss'],
    ['NC',                                    'NC'],
    ['SCL (I&sup2;C CLOCK)',                  'SCL'],
    ['SDA (I&sup2;C DATA)',                   'SDA'],
    ['NC',                                    'NC'],
    ['A0 I&sup2;C Address Selection)',        'A0',             'guide' => 'i2c-a0'],
    ['A1 I&sup2;C Address Selection)',        'A1',             'guide' => 'i2c-a1'],
    ['A2 I&sup2;C Address Selection)',        'A2',             'guide' => 'i2c-a2'],
    ['Reset',                                 'RESET'],
    ['Interupt B',                            'IB'],
    ['Interupt A',                            'IA'],
    ['GPA 0',                                 'GPA 0',          'gpio' => 0],
    ['GPA 1',                                 'GPA 1',          'gpio' => 1],
    ['GPA 2',                                 'GPA 2',          'gpio' => 2],
    ['GPA 3',                                 'GPA 3',          'gpio' => 3],
    ['GPA 4',                                 'GPA 4',          'gpio' => 4],
    ['GPA 5',                                 'GPA 5',          'gpio' => 5],
    ['GPA 6',                                 'GPA 6',          'gpio' => 6],
    ['GPA 7',                                 'GPA 7',          'gpio' => 7]
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
                    <p>Pinout guide &amp; interactive web GUI for MCP23017 and MCP23008 based Raspberry Pi expansion boards. Select a pin from the board to get started.</p>
                    <p>You can now add your own boards! Check-out our guide on <a href="https://github.com/plasmadancom/HAT-GUI#add-your-own-board">GitHub</a>.</p>
                    <h2>Easy Installer</h2>
                    <p>Our easy installer takes care of the setup process automatically.</p>
                    <pre class="bash">
<span class="code-highlight">sudo wget</span> https://git.plasmadan.com/install.sh
<span class="code-highlight">sudo sh</span> install.sh</pre>
                    <p>This script will automatically enable I2C, install the required packages and setup the Web GUI.</p>
                    <p>Alternatively, you can install manually. See our <a href="https://github.com/plasmadancom/HAT-GUI#setup-guide">setup guide</a>.</p>
<?php $intro = ob_get_clean(); ?>