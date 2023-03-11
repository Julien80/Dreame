#!/bin/bash

echo "Launch pre-install of dreame dependancy"

sudo apt remove -y rustc
sudo apt remove -y cargo
sudo curl -o rustup.sh -sSf https://sh.rustup.rs
sudo chmod +x rustup.sh
sudo ./rustup.sh -y
sudo rm rustup.sh
sudo ln -s /root/.cargo/bin/rustc /usr/bin/rustc
sudo ln -s /root/.cargo/bin/cargo /usr/bin/cargo 
