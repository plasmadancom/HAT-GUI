#!/bin/sh
# install.sh Easy installer for HAT-GUI

GITHUB_USERNAME="plasmadancom"


CONFIG="/boot/config.txt"
VSFTPD_CONF="/etc/vsftpd.conf"
APACHE_CONF="/etc/apache2/apache2.conf"
CONFIRM_INPUT="Please answer yes or no."
GUI_FILES="https://github.com/$GITHUB_USERNAME/HAT-GUI/trunk/gui"

INSTALL_WEBROOT=false
INSTALL_FTP=false

# Arguments: 1 search, 2 replace, 3 setting name, 4 file
update_file() {
    if grep -Fq $1 $4
        then
            echo "Update $3 found in $4 ..."
            sed -i "/$1/c\\$2" $4
    else
        echo "Add $3 in $4 ..."
        echo "$2" >> $4
    fi
}


while true; do
    read -p "Install HAT-GUI at web root? This will empty the web root! [Y/n]" yn
    case $yn in
        [Yy]* )
            INSTALL_WEBROOT=true
            break
            ;;
        [Nn]* )
            while true; do
                read -p "Enter subdirectory to install HAT-GUI [hats]:" SUBDIR
                if [ -z "$SUBDIR" ]
                then
                    SUBDIR="hats"
                    break
                else
                    break
                fi
            done
            echo "HAT-GUI will be installed in subdirectory: $SUBDIR"
            break
            ;;
        * ) echo $CONFIRM_INPUT;;
    esac
done


while true; do
    read -p "Install vsftpd? [Y/n] " yn
    case $yn in
        [Yy]* )
            INSTALL_FTP=true
            break
            ;;
        [Nn]* ) break;;
        * ) echo $CONFIRM_INPUT;;
    esac
done


echo "Enable I2C interface ..."
raspi-config nonint do_i2c 0

update_file "dtparam=i2c_arm" "dtparam=i2c_arm=on" "I2C interface setting" $CONFIG
update_file "dtparam=i2c_vc" "dtparam=i2c_vc=on" "I2C bus 0" $CONFIG
update_file "dtparam=i2c1" "dtparam=i2c1=on" "I2C bus 1" $CONFIG
update_file "dtparam=i2c_baudrate" "dtparam=i2c_baudrate=400000" "I2C bus baudrate setting" $CONFIG


#echo "Update package lists and upgrade ..."
#apt-get update -y
#apt-get upgrade -y


echo "Install HAT-GUI required packages ..."
apt-get install i2c-tools git-core gcc make apache2 php libapache2-mod-php subversion -y

echo "Clone wiringpi and build ..."
git clone https://github.com/WiringPi/WiringPi --branch master --single-branch ~/wiringpi
cd ~/wiringpi
./build
cd ~

# Enable rewrite module
a2enmod rewrite

echo "Update AllowOverride None setting in $APACHE_CONF ..."

perl -i -p0e "s/<Directory \/var\/www\/>\n\tOptions Indexes FollowSymLinks\n\tAllowOverride None\n\tRequire all granted\n<\/Directory>/<Directory \/var\/www\/>\n\tOptions Indexes FollowSymLinks\n\tAllowOverride All\n\tRequire all granted\n<\/Directory>/gms" $APACHE_CONF

echo "Add Apache to i2c group ..."
adduser www-data i2c

echo "Add i2c group permissions ..."
chmod g+rw /dev/i2c-1

echo "Create new udev rule 10-local_i2c_group.rules ..."
echo 'KERNEL=="i2c-[0-1]*", GROUP="i2c"' | tee /etc/udev/rules.d/10-local_i2c_group.rules

echo "Install HAT-GUI ..."

# Install at web root?
if [ "$INSTALL_WEBROOT" = true ]
    then
        rm -rf /var/www/html/*
        svn checkout $GUI_FILES /var/www/html
else
    mkdir /var/www/html/$SUBDIR
    svn checkout $GUI_FILES /var/www/html/$SUBDIR
    sed -i "/\$install_subdir = '';/c\$install_subdir = '"$SUBDIR"';" /var/www/html/$SUBDIR/index.php
fi


# Install vsftpd?
if [ "$INSTALL_FTP" = true ]
    then
        apt-get install vsftpd -y
        chown -R pi /var/www
        update_file "write_enable" "write_enable=YES" "write_enable setting" $VSFTPD_CONF
        update_file "force_dot_files" "force_dot_files=YES" "force_dot_files setting" $VSFTPD_CONF
        service vsftpd restart
fi

echo "Restart apache..."
systemctl restart apache2
chmod -R 755 /var/www
hostname -I
echo "HAT-GUI Installed."

exit 0