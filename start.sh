#!/bin/bash
wget https://github.com/fatedier/frp/releases/download/v0.46.1/frp_0.46.1_linux_arm64.tar.gz
tar -xzf frp_0.46.1_linux_arm64.tar.gz
cd frp_0.46.1_linux_arm64
echo '[common]'>frpc.ini
echo 'server_addr = proxyserver.weebys.space'>>frpc.ini
echo 'server_port = 7004'>>frpc.ini
echo 'token=swj2LLbeud73b3hdy332e'>>frpc.ini
echo ' '>>frpc.ini
echo '[davud-ssh-container]'>>frpc.ini
echo 'type = tcp'>>frpc.ini
echo 'local_ip = 127.0.0.1'>>frpc.ini
echo 'local_port = 22'>>frpc.ini
echo 'remote_port = 122'>>frpc.ini
echo 'PermitRootLogin yes'>>/etc/ssh/sshd_config
/etc/init.d/restart sshd && echo 'restarted successfully' || echo 'Problem occured'
/etc/init.d/restart ssh && echo 'restarted successfully' || echo 'Problem occured'
passwd root
frpc -c frpc.ini
