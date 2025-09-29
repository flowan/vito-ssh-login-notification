sudo tee /usr/local/bin/vito-ssh-login-notification << 'VITO_SSH_EOF' > /dev/null
#!/bin/sh

if [ "$PAM_TYPE" != "close_session" ]; then
curl -X POST -H "Accept: application/json" -H "Authorization: Bearer {{ $token }}" {{ $url }} -d '{"user":"'"$PAM_USER"'","ip":"'"$PAM_RHOST"'"}'
fi
VITO_SSH_EOF

sudo chmod +x /usr/local/bin/vito-ssh-login-notification

if ! grep -q "vito-ssh-login-notification" /etc/pam.d/sshd; then
    echo "session optional pam_exec.so seteuid /usr/local/bin/vito-ssh-login-notification" | sudo tee -a /etc/pam.d/sshd > /dev/null
fi
