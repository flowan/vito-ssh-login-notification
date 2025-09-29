if grep -q "vito-ssh-login-notification" /etc/pam.d/sshd; then
    sudo sed -i '/vito-ssh-login-notification/d' /etc/pam.d/sshd
fi

sudo rm -f /usr/local/bin/vito-ssh-login-notification
