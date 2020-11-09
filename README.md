![php 5.4+](https://img.shields.io/badge/php-v5.4%2B-blue) [![maintained](https://img.shields.io/badge/maintained-yes-green.svg)](https://github.com/plasmadancom/HAT-GUI/graphs/commit-activity) ![licence](https://img.shields.io/github/license/plasmadancom/HAT-GUI) [![demo](https://img.shields.io/website?down_color=lightgrey&down_message=offline&label=demo&up_color=green&up_message=online&url=https%3A%2F%2Fio.plasmadan.com)](https://io.plasmadan.com)

# HAT-GUI

<p align="center">
    <a href="https://io.plasmadan.com" target="_blank" rel="nofollow">
        <img alt="HAT-GUI" src="/img/hat-gui-mockup.gif">
    </a>
</p>

Once installed on your Raspberry Pi, this interactive GUI allows quick &amp; easy control of any MCP23017 or MCP23008 based expansion boards. The GUI is fully responsive and adapts to any screen size.

Check-out the [Live Demo.](https://io.plasmadan.com)

## Easy Installer

Our easy installer takes care of the setup process automatically.

```
sudo wget https://git.plasmadan.com/install.sh
sudo sh install.sh
```

This script will automatically enable I2C, install the required packages and setup HAT-GUI.

Alternatively, you can install manually. See our [setup guide](#setup-guide).

## Add Your Own Board

Fork this repository, copy any existing board within /gui and change it your board name. Your directory will contain a board.php file which details everything about your board. The pinout is set up in php array format, change this to your board's pinout and modify the stylesheet to display your board correctly. See notes within board.php for configuration details.

You can use your own version of the easy installer to streamline installation of your custom board onto your Pi, simply edit install.sh and change GITHUB_USERNAME to your own.

```
sudo wget https://github.com/USERNAME/HAT-GUI/blob/master/install.sh
sudo sh install.sh
```

Equally, you can delete any boards you don't need by simply deleting the directory.

## Python Examples

We have provided some example Python scripts to get you started (see [here](https://github.com/plasmadancom/HAT-GUI/tree/master/python_examples)).

## Contributing

If HAT-GUI is useful to you please help improve it by contributing. If you have a board you want to add to the main branch submit a pull request for approval.

We have a few ideas for improvements which we're looking to implement in the future:

* Ditch WiringPi (deprecated)
* Convert to Node.js
  - Would be awesome once done, but requires completely re-building from scratch. (╥_╥)

### Overlays

Overlays can be used to add interactive elements to your board such as relays, buttons or LEDs.
Note: the board stylesheet will load after the main stylesheet so you can override things if required.

You can also add an image overlay to the entire board to add non-interactive details such as board markings, cut-outs or connectors. This should be precisely 600x518px (WxH) to fit correctly, as it will be automatically scaled to fit on mobile devices etc.

```
.board:before {
    background-image: url("img/overlay.png");
}
```

#### Dynamic Overlays

Dynamic overlays are defined using a class name within the CSS section of board.php. They can be named anything, the name is only used to target them in your stylesheet. Dynamic overlays are used as an alternate way to select an I/O using a graphical element such as a relay or button.

Dynamic overlays can all be targetted at once using the `dynamic-overlay` class, which is added automatically.

#### Button Overlays

Dynamic overlays can also optionally toggle the associated GPIO when clicked. Perfect for emulating button pushes, it can also improve GUI usability, especially on mobile devices. CSS classes that contain "button" will automatically work as buttons. This can be applied to any dynamic overlay, but not dynamic LEDs.

Alternatively, setting the optional global `button_mode` parameter to true will force all dynamic overlays to work as buttons.

#### LED Overlays

LED overlays work in a similar way to dynamic overlays, except they are displayed according to GPIO status. CSS classes that contain "led" are considered to be LED overlays. LED overlays can all be targetted at once using the `dynamic-led` class, which is added automatically.

E.g., if you have 4 GPIOs with classes led1, led2, led3, led4 respectively. You can add an overlay to use for those LEDs with a small snippet of CSS. See [Appliance HAT](https://io.plasmadan.com/appliancehat/) for a working example.

```
.overlay .dynamic-led {
    width: 30px;
    height: 60px;
    background: #c2321d;
    top: 100px;
}

.overlay .led2 {
    top: 200px;
}

.overlay .led3 {
    top: 300px;
}

.overlay .led4 {
    top: 400px;
}

```

This will set all LEDs to the same style and position them 100px apart vertically.

# Setup Guide

## Prerequisites

Raspberry Pi with Raspberry Pi OS:
https://www.raspberrypi.org/downloads/raspberry-pi-os/

I recommend a clean Raspberry Pi OS install before proceeding.

Tip: For headless setup, SSH can be enabled by placing a file named 'ssh' (no extension) onto the boot partition of the SD card.

## Enable I2C

I2C must be enabled in raspi-config to allow I2C based HATs to communicate with Raspberry Pi.

```
sudo raspi-config
```

Select 5 Interfacing Options, then P5 I2C. A prompt will appear asking Would you like the I2C interface to be enabled?, select Yes, exit the utility and reboot your Raspberry Pi.

```
sudo reboot
```

Update your Raspberry Pi to ensure all the latest packages are installed.

```
sudo apt update
sudo apt upgrade
```

Install I2C-Tools

```
sudo apt install i2c-tools -y
```

Enable i2c_vc so your Raspberry Pi can detect and read the EEPROM.

```
sudo sh -c "echo 'dtparam=i2c_vc=on' >> /boot/config.txt"
```

For recent versions of the Raspberry Pi (3.18 kernel or later) you will need to add `dtparam=i2c1=on` to the end of `/boot/config.txt`.

```
sudo sh -c "echo 'dtparam=i2c1=on' >> /boot/config.txt"
```

Optional: Increase the I2C bus speed by adding the i2c_baudrate paramter to `/boot/config.txt`, then reboot.

```
sudo sh -c "echo 'dtparam=i2c_baudrate=400000' >> /boot/config.txt"
sudo reboot
```

Test if your HAT is detectable.

```
sudo i2cdetect -y 1
```

You should see a grid of all populated I2C devices.

<p align="center">
    <img alt="I2cdetect output" src="/img/i2cdetect.gif">
</p>

## Install WiringPi

```
sudo apt install wiringpi -y
```

If you wish to write your own scripts using Python, you will need to install WiringPi for Python also.

```
sudo apt install python-pip -y
```

Install WiringPi for Python.

```
sudo pip install wiringpi
```

## Install Apache & PHP

```
sudo apt install apache2 php libapache2-mod-php -y
```

Enable mod_rewrite.

```
sudo a2enmod rewrite
```

Optional: Add i2c permissions to allow Apache access to i2c bus. Used purely to detect board connection status.

```
adduser www-data i2c
chmod g+rw /dev/i2c-1
echo 'KERNEL=="i2c-[0-1]*", GROUP="i2c"' | tee /etc/udev/rules.d/10-local_i2c_group.rules
```

AllowOverride None is now default [since Apache 2.3.9](https://httpd.apache.org/docs/2.4/mod/core.html), we need to change it back to AllowOverride All.

Edit apache2.conf.

```
sudo nano /etc/apache2/apache2.conf
```

Find `AllowOverride None` within `Directory /var/www/`.

```
<Directory /var/www/>
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
</Directory>
```

Change it to `AllowOverride All`.

Save and exit nano, then restart Apache.

```
sudo systemctl restart apache2
```

Test the webserver is working. Navigate to http://localhost/ on the Pi itself, or http://192.168.1.10 (whatever the Pi's IP address is) from another computer on the network. Use the snippet below to get the Pi's IP address in command line.

```
hostname -I
```

## Install HAT-GUI

You need to clone the web GUI files from the `/gui` subdirectory, to do that we need to install subversion.

**Note: The html files are for the live demo, you don't need to install them.**

```
sudo apt install subversion -y
```

Choose where to install HAT-GUI.

### Option 1: Install at Web Root

Empty default Apache files and install HAT-GUI.

```
sudo rm -rf /var/www/html/*
sudo svn checkout https://github.com/plasmadancom/HAT-GUI/trunk/gui /var/www/html
```

### Option 2: Subdirectory Install

HAT-GUI can be installed in any subdirectory. In this example we'll create a new subdirectory `hats`.

```
mkdir /var/www/html/hats
sudo svn checkout https://github.com/plasmadancom/HAT-GUI/trunk/gui /var/www/html/hats
```

### Permissions

Be sure to set file permissions to 755 in the web directory.

```
sudo chmod -R 755 /var/www
```

That's it! reload the web page and you should see the HAT-GUI interface.

## Optional: Install vsftpd for Easier File Editing

```
sudo apt install vsftpd -y
```

Change user for vsftpd.

```
sudo chown -R pi /var/www
```

Edit vsftpd.conf.

```
sudo nano /etc/vsftpd.conf
```

Uncomment the following line:

```
write_enable=YES
```

Add the following line:

```
force_dot_files=YES
```

Save and exit nano, then restart vsftpd.

```
sudo service vsftpd restart
```

You should now be able to login via FTP.

## License

MIT © Dan Jones - [PlasmaDan.com](https://plasmadan.com)
