/*
 * scripts.js - jQuery scripts for HAT-GUI
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

$(document).ready(function() {
    // Parse settings from DOM
    var demo_mode = $('#demo_mode').val() == 'true';
    var button_mode = $('#button_mode').val() == 'false';
    var pinBase = parseInt($('#pin_base').val());
    var extension = $('#extension').val().toLowerCase();
    var directory = $('#directory').val();
    
    // Return query string
    function get_query(q) {
        var query = window.location.search.substring(1);
        var arr = query.split('&');
        
        // Iterate query parameters
        for (var i = 0; i < arr.length; i++) {
            var val = arr[i].split('=');
            
            // Return if matched
            if(val[0] == q) return val[1];
        }
        
        return false;
    }
    
    // Return / update I2C address
    function i2c_addr(u = false) {
        // Update
        if (u != false) {
            // Convert I2C address to binary: A0: 1's, A1: 10's, A2: 100's
            var dec = parseInt(u-32);
            var bin = (dec >>> 0).toString(2);
            
            // Kinda crazy method, but it's fast and it works!
            var pad = ('000' + bin).slice(-3).split('').reverse().join('');
            
            // Add pullup class to relevant pins
            for (var i = 0; i < 3; i++) {
                var i2c = $('.gpios .i2c-a' + i);
                
                pad.charAt(i) == 1 ? i2c.addClass('pullup') : i2c.removeClass('pullup');
            }
            
            return true;
        }
        
        var i2c0 = $('.gpios .i2c-a0').hasClass('pullup') ? 1:0;
        var i2c1 = $('.gpios .i2c-a1').hasClass('pullup') ? 10:0;
        var i2c2 = $('.gpios .i2c-a2').hasClass('pullup') ? 100:0;
        
        // Combine and convert to decimal
        var dec = parseInt(i2c0 + i2c1 + i2c2, 2);
        
        return '0x2' + dec;
    }
    
    // Return pin status in plain text
    function print_status(i) {
        if ($.isPlainObject(i)) {
            if (Object.values(i).every((val, i, arr) => val === arr[0])) return i[0] ? 'active' : 'inactive';
            
            return Object.values(i).join(', ');
        }
        
        return i ? 'active' : 'inactive';
    }
    
    // Return pin logic in plain text
    function print_logic(i, mode='out') {
        // Swap status in pull-up mode
        var i = mode == 'up' ? !i : i;
        
        if (mode != 'in') return i ? ' (high)' : ' (low)';
        
        return i ? ' (high)' : ' (low or floating)';
    }
    
    // Return mode in plain text
    function print_mode(i) {
        switch (i) {
            case 'out':
                return 'output mode';
            
            case 'in':
                return 'input mode';
            
            case 'up':
                return 'pull-up mode';
            
            default:
                return 'must be initialised before use!';
        }
    }
    
    // Print to log
    function print_log(log, response=false) {
        var out = $('.log');
        
        if (response) {
            // Append response to log
            if (response !== true) {
                if (out.length) out.find('#' + response).after(log);
                
                return;
            }
            
            // Mark new log lines awaiting response
            var response = Date.now();
            log += '<span id="' + response + '"></span>';
        }
        
        log += "\n";
        
        // Append log and scroll
        if (out.length) out.append(log).scrollTop(out[0].scrollHeight - out.height());
        
        return response;
    }
    
    // Append result to request
    function log_response(request, result) {
        print_log('Done (output ' + print_status(result) + ').', request)
    }
    
    // Update GUI
    function update_gui(gpios, gpio) {
        $.each(gpios, function(p, result) {
            var pin = $('.gpio' + p);
            var a = pin.find('a');
            var num = a.data('pin');
            var led = $('.dynamic-led.pin' + num);
            
            pin.removeClass('on');
            led.removeClass('on');
            a.data('logic', result);
            
            if (result) {
                pin.addClass('on');
                led.addClass('on');
            }
        });
        
        $('.pindata .status').html(print_status(gpios[gpio]));
        $('.pindata .logic').html(print_logic(gpios[gpio]));
    }
    
    // Pin selection
    function pin_click(e) {
        // Collect GPIO data
        var pin = parseInt(e.data('pin'));
        var gpio = parseInt(e.data('gpio'));
        var logic = parseInt(e.data('logic'));
        var guide = e.data('guide');
        var mode = e.data('mode');
        var name = e.data('name');
        
        // Reset GUI
        $('.dynamic-overlay').removeClass('selected');
        $('.board .active').removeClass('active');
        $('.guides .output_toggle').addClass('button-disabled');
        $('.page .intro, .guides .pinstatus, .guides .guide, .guides .button').hide();
        $('.guides .pindata, .page .log, .status-wrapper').show();
        
        // Highlight selected dynamic-overlay
        $('.dynamic-overlay.pin' + pin).addClass('selected');
        
        // Highlight selected pin
        $('.board .pin' + pin + ', .board .' + guide).addClass('active');
        
        // Interactive guide
        var interactive = $('.guides .interactive, .guides .pinstatus');
        
        switch (mode) {
            case 'out':
                interactive.show();
                
                // Enable toggle buttons for outputs
                $('.guides .toggle').show();
                $('.guides .output-toggle').removeClass('button-disabled');
                
                break;
            
            case 'unconfigured':
                interactive.show();
                
                break;
            
            case 'i2c':
                $('.guides .' + guide).show();
                $('.guides .i2c-swap').show();
                
            case undefined:
                $('.guides .' + guide).show();
                
                break;
            
            default:
                interactive.show();
        }
        
        // Update DOM
        $('.guides .pinname').html(name);
        $('.guides .physpin').html(pin);
        $('.guides .pinbase').html(pinBase);
        $('.guides .io').html(pinBase + gpio);
        $('.guides .output-toggle').data('gpio', gpio);
        $('.guides .i2c-swap').data('i2c', guide);
        $('.guides .status').html(print_status(logic));
        $('.guides .logic').html(print_logic(logic, mode));
        $('.guides .mode').html(print_mode(mode));
        $('.guides .i2c_addr').html(i2c_addr());
        $('.guides .i2c-table a[href="?i2c=' + i2c_addr() + '"]').closest('tr').addClass('selected');
    }
    
    // Toggle GPIO
    function toggle(gpio) {
        // State is reversed because this is a click event, not a change event
        var request = print_log('Toggle gpio ' + parseInt(pinBase + gpio) + '... ', true);
        
        // Spoof result for demo
        if (demo_mode) {
            // Swap logic
            var result = $('.gpio' + gpio).find('a').data('logic') ? 0 : 1;
            
            // Spoof response delay
            setTimeout(function() {
                log_response(request, result);
                update_gui({[gpio]:result}, gpio);
            }, 100);
            
            return;
        }
        
        $.ajax({
            type: 'POST',
            url: directory + 'src/inc/functions.php',
            data: {'toggle' : gpio, 'extension' : extension, 'pinbase' : pinBase, 'i2c' : i2c_addr()},
            dataType: 'json',
            timeout: 5000,
            success: function(r) {
                if (!$.trim(r)) {
                    print_log('Ajax error!', request);
                    return;
                }
                
                var result = JSON.parse(r[0]);
                
                log_response(request, result);
                update_gui({[gpio]:result}, gpio);
            },
            complete: function() {
                
            },
            error: function(xhr, textStatus, errorThrown) {
                try {
                    print_log(JSON.parse(xhr.responseText), request);
                }
                catch(e) {
                    print_log(textStatus, request);
                }
            }
        });
    }
    
    // Click on pin
    $('.board a').on('click', function(e) {
        e.preventDefault();
        
        pin_click($(this));
    });
    
    // Click on dynamic block (relay, led etc)
    $('.overlay div').on('click', function(e) {
        e.preventDefault();
        
        // Find CH in DOM
        var ch = $(this).attr('class').split(/\s+/)[0];
        var elm = $('.' + ch + ' a');
        
        pin_click(elm);
        
        if (button_mode || $(this).is('[class*="button"]')) {
            var gpio = parseInt(elm.data('gpio'));
            
            toggle(gpio);
        }
    });
    
    // Toggle GPIO
    $('.page .output-toggle').on('click', function(e) {
        e.preventDefault();
        
        if ($(this).hasClass('button-disabled')) return;
        
        // Collect GPIO data
        var gpio = parseInt($(this).data('gpio'));
        
        toggle(gpio);
    });
    
    // Write GPIOs
    $('.page .toggle-all').on('click', function(e) {
        e.preventDefault();
        
        var gpio = $('.guides .output-toggle').data('gpio');
        var gpios = [];
        
        $(".gpios a[data-gpio]").not('a[data-mode="unconfigured"]').each(function() {
            gpios.push($(this).data('gpio'));
        });
        
        // Set on or off
        var write = $(this).hasClass('off') ? 0 : 1;
        
        var request = print_log('Write all (initialised) gpios... ', true);
        
        // Spoof result for demo
        if (demo_mode) {
            var result = {};
            
            // Spoof each gpio
            for (i = 0; i < 16; i++) result[i] = write;
            
            // Spoof response delay
            setTimeout(function() {
                log_response(request, result);
                update_gui(result, gpio);
            }, 300);
            
            return;
        }
        
        $.ajax({
            type: 'POST',
            url: directory + 'src/inc/functions.php',
            data: {'gpios' : gpios, 'write' : write, 'extension' : extension, 'pinbase' : pinBase, 'i2c' : i2c_addr()},
            dataType: 'json',
            timeout: 5000,
            success: function(result) {
                if (!$.trim(result)) {
                    print_log('Ajax error!', request);
                    return;
                }
                
                log_response(request, result);
                update_gui(result, gpio);
            },
            complete: function() {
                
            },
            error: function(xhr, textStatus, errorThrown) {
                try {
                    print_log(JSON.parse(xhr.responseText), request);
                }
                catch(e) {
                    print_log(textStatus, request);
                }
            }
        });
    });
    
    // Check for I2C address parameter
    if (get_query('i2c') != false) i2c_addr(get_query('i2c'));
    
    // Notify user if development mode enabled
    if (demo_mode) print_log('Demo mode enabled.');
    else {
        // Notify user if board detected @ I2C address
        if ($(".connection").hasClass('connection-error')) {
            print_log('Device connection error: ' + i2c_addr());
        }
        else print_log('Device ' + i2c_addr() + ' connected.');
    }
});