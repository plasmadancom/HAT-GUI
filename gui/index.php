<?php

/*
 * index.php - HAT-GUI Index
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

$pinBase = 100;
$demo_mode = false;

require_once __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . '/board.php';
require_once __DIR__ . '/src/inc/functions.php';

?>
<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $board->page_title ?></title>
    
    <!-- Search Engine -->
    <meta name="description" content="<?= $board->description ?>">
    <meta name="image" content="<?= $board->image_url ?>">
    
    <!-- Social -->
    <meta property="og:title" content="<?= $board->page_title ?>">
    <meta property="og:description" content="<?= $board->description ?>">
    <meta property="og:image" content="<?= $board->image_url ?>">
    <meta property="og:url" content="<?= 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" ?>">
    <meta property="og:site_name" content="HAT-GUI">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image:alt" content="HAT-GUI - Interactive UI &amp; Pinout Guide">
    
    <link href="/src/css/style.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <script src="/src/js/jquery.min.js"></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</head>
<body>
    <!-- Demo mode -->
    <input id="demo_mode" type="hidden" value="<?= $demo_mode ? 'true' : 'false' ?>">
    
    <!-- Settings -->
    <input id="pin_base" type="hidden" value="<?= $pinBase ?>">
    <input id="extension" type="hidden" value="<?= $board->extension ?>">
    <div class="container">
        <div class="float wrapper">
            <!-- GitHub Buttons -->
            <div class="github">
                <a class="github-button" href="https://github.com/plasmadancom/HAT-GUI" data-icon="octicon-star" data-size="large" data-show-count="true">Star</a>
                <a class="github-button" href="https://github.com/plasmadancom/HAT-GUI/fork" data-icon="octicon-repo-forked" data-size="large">Fork on GitHub</a>
            </div>
            <div class="orientation">
                <div class="board">
                    <div class="header">
                        <ul><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul>
                    </div>
                    <div class="pinout">
                        <div class="gpiobase">
                            <div class="gpios">
                                <ul>
<?php

// Print pin data from board.php
foreach ($parsed_pins as $pin => $i) {
?>
                                    <li class="pin<?= $pin . $i['css'] ?>"><a href="#" data-pin="<?= $pin ?>"<?= $i['data'] ?> data-name="<?= $i['name'] ?>"><span class="num"><?= $pin ?></span><?= $i['shortname'] ?><span class="pin"></span></a></li>
<?php
}
?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="overlay">
<?php

// Print any overlay css classes from board.php
foreach ($css_classes as $c) {
?>
                        <div class="<?= 'pin' . $c['pin'] . $c['css'] ?>"></div>
<?php
}
?>
                    </div>
                </div>
            </div>
        </div>
        <div class="page">
            <ul class="menu">
                <li<?= cur_page('') ?>><a href="/">MCP23017</a>
                <li<?= cur_page('MCP23008') ?>><a href="/MCP23008">MCP23008</a>
<?php

$boards = array();

// Scrape the web directory for addtional boards and include them
foreach (glob($_SERVER['DOCUMENT_ROOT'] . '/*', GLOB_ONLYDIR) as $dir) {
    $dir =  basename($dir);
    
    // Ignore these
    if ($dir == 'MCP23008' or $dir == 'src' or !file_exists($dir . '/board.php')) continue;
    
    // Include board.php from directory
    include $dir . '/board.php';
    
    // If a board image exists, use it
    $board_img = file_exists($dir . '/img/board.png') ? '<img src="/' . $dir . '/img/board.png" alt="' . $board_name . '">' : '';
    
    // Save for later
    $boards[$board_name] = ['dir' => $dir, 'img' => $board_img];
    
    // Print menu link to each board
?>
                <li<?= cur_page($dir) ?>><a href="/<?= $dir ?>"><?= $board_name ?></a>
<?php
}


// Board connection status defaults
$connection = '0x2' . ($i2c_addr-32);
$connection_status = 'connection';

// Skip for demo mode
if ($demo_mode !== false) {
?>
                <li class="demo_label"><a href="#">Demo<span class="demo_tooltip">Test environment to demo the GUI without a Raspberry Pi.</span></a>
<?php
}

// Connected
else if (detect_i2c($i2c_addr)) {
    $connection .= ' Connected';
    $connection_status .= ' connected';
}

// Not connected
else {
    $connection .= ' Not Found';
    $connection_status .= ' connection-error';
}
?>
            </ul>
            <div class="page-wrapper">
                <div class="intro">
                    <h1><?= $board->board_name ?></h1><span class="<?= $connection_status ?>"><?= $connection ?></span>
<?php
// Print intro html from board.php
if (!empty($board->intro)) echo $board->intro;

// Main page
if ($_SERVER['REQUEST_URI'] == '/') {
?>
                    <h2>Boards</h2>
                    <ul class="boards">
<?php
    // Print list of each board with image if available
    foreach ($boards as $n => $b) {
?>
                        <li><a href="/<?= $b['dir'] ?>"><?= $b['img'] ?><span><?= $n ?></span></a></li>
<?php
    }
?>
                    </ul>
                    <div class="clear"></div>
<?php
}

// MCP23008 Page
else if ($_SERVER['REQUEST_URI'] == '/MCP23008/') {}

// Custom boards
else {
?>
                    <table class="details">
                        <tbody>
                            <tr>
                                <td>
                                    <h2>Details</h2>
                                    <ul>
                                        <li>Based on <?= strtoupper($board->extension) ?> Expander</li>
                                        <li><a href="<?= $board->github ?>">GitHub Repository</a></li>
                                        <li><a href="<?= $board->buy ?>">Buy Now</a></li>
                                    </ul>
                                </td>
<?php
    // Display image if available
    if (!empty($boards[$board->board_name]['img'])) {
?>
                                <td>
                                    <?= $boards[$board->board_name]['img'] ?>

                                </td>
<?php
    }
?>
                            </tr>
                        </tbody>
                    </table>
<?php
}
?>
                </div>
                <div class="guides">
                    
                    <!-- Placeholder gpio pin data -->
                    <div class="guide pindata">
                        <h3 class="pinname"></h3>
                        <ul>
                            <li>Physical pin <span class="physpin"></span></li>
                            <li class="pinstatus">WiringPi pin <span class="io"></span></li>
                            <li class="pinstatus">Currently <span class="status-wrapper"><span class="status"></span><span class="logic"></span>, </span><span class="mode"></span></li>
                        </ul>
                        
                        <!-- Toggle buttons -->
                        <a href="#" class="button toggle output-toggle" data-gpio="8">Toggle Output</a>
                        <a href="#" class="button i2c-swap" data-i2c="i2c-a0">Swap I2C Jumper</a>
                        <a href="#" class="button toggle toggle-all">All On</a>
                        <a href="#" class="button toggle toggle-all off">All Off</a>
                    </div>
                    
                    <!-- WiringPi guide -->
                    <div class="guide interactive">
                        <h3>WiringPi Guide</h3>
                        <h4>Read</h4>
                        <pre>gpio -x <?= $board->extension ?>:<span class="pinbase" title="Pin Base">100</span>:<span class="i2c_addr" title="I&sup2;C Address">0x20</span> read <span class="io">108</span></pre>
                        <h4>Write</h4>
                        <pre>gpio -x <?= $board->extension ?>:<span class="pinbase" title="Pin Base">100</span>:<span class="i2c_addr" title="I&sup2;C Address">0x20</span> mode <span class="io">108</span> out<br>gpio -x <?= $board->extension ?>:<span class="pinbase" title="Pin Base">100</span>:<span class="i2c_addr" title="I&sup2;C Address">0x20</span> write <span class="io">108</span> 1</pre>
                    </div>
                    
                    <!-- VDD guide -->
                    <div class="guide vdd">
                        <ul>
                            <li>Operating voltage: - 1.8V to 5.5V</li>
                            <li>Maximum current into VDD pin: 125mA</li>
                        </ul>
                    </div>
                    
                    <!-- VSS guide -->
                    <div class="guide vss">
                        <ul>
                            <li>Maximum current out of VSS pin: 150mA</li>
                        </ul>
                    </div>
                    
                    <!-- I2C address selection guide -->
                    <div class="guide i2c-a0 i2c-a1 i2c-a2">
                        <h3>Address Selection</h3>
                        <table class="i2c-table">
                            <thead>
                                <tr>
                                    <th>Address</th>
                                    <th>A2</th>
                                    <th>A1</th>
                                    <th>A0</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><a href="?i2c=0x20">0x20</a></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><a href="?i2c=0x21">0x21</a></td>
                                    <td></td>
                                    <td></td>
                                    <td>&squf;</td>
                                </tr>
                                <tr>
                                    <td><a href="?i2c=0x22">0x22</a></td>
                                    <td></td>
                                    <td>&squf;</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><a href="?i2c=0x23">0x23</a></td>
                                    <td></td>
                                    <td>&squf;</td>
                                    <td>&squf;</td>
                                </tr>
                                <tr>
                                    <td><a href="?i2c=0x24">0x24</a></td>
                                    <td>&squf;</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><a href="?i2c=0x25">0x25</a></td>
                                    <td>&squf;</td>
                                    <td></td>
                                    <td>&squf;</td>
                                </tr>
                                <tr>
                                    <td><a href="?i2c=0x26">0x26</a></td>
                                    <td>&squf;</td>
                                    <td>&squf;</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><a href="?i2c=0x27">0x27</a></td>
                                    <td>&squf;</td>
                                    <td>&squf;</td>
                                    <td>&squf;</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

<?php

// Print any additional guides from board.php
if (!empty($board->guide)) echo $board->guide;

?>

                </div>
            </div>
            
            <!-- System log area -->
            <textarea class="log hide" placeholder="System log..." readonly></textarea>
        </div>
    </div>
    <div class="footer">
        <p>Spotted a problem? Submit an Issue or a Pull Request on our <a href="https://github.com/plasmadancom/HAT-GUI">GitHub repository</a>!</p>
        <p>Built by <a href="https://plasmadan.com">PlasmaDan</a>. Tweet us at <a href="https://twitter.com/Plasma_Dan">@Plasma_Dan</a>.</p>
    </div>
    <script src="/src/js/scripts.js"></script>
</body>
</html>