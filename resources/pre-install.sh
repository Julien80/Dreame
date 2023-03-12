#!/bin/bash

RUST_VERSION="1.48.0"
PYTHON_VERSION="3.8.2"

echo "Launch pre-install of dreame dependency"

if ! python${PYTHON_VERSION:0:3} -c "import sys; assert sys.version_info >= (3, 8, 2)" >/dev/null 2>&1; then
    echo "Python ${PYTHON_VERSION} or later is not installed. Installing now..."
    sudo apt-get update -q
    sudo apt-get install -y curl build-essential libffi-dev libssl-dev zlib1g-dev libbz2-dev libreadline-dev libsqlite3-dev wget llvm libncurses5-dev xz-utils tk-dev libxml2-dev libxmlsec1-dev libffi-dev liblzma-dev
    curl -O https://www.python.org/ftp/python/${PYTHON_VERSION}/Python-${PYTHON_VERSION}.tar.xz
    tar -xf Python-${PYTHON_VERSION}.tar.xz
    cd Python-${PYTHON_VERSION}
    echo "Running ./configure"
    ./configure --enable-optimizations
    echo "Running make"
    make -j 4
    echo "Running make altinstall"
    sudo make altinstall
fi

sudo apt remove -y rustc
sudo apt remove -y cargo
sudo apt autoremove
echo "Installing rustup"
curl --proto '=https' --tlsv1.2 -sSf https://sh.rustup.rs | sh -s -- -y --no-modify-path
echo "Updating rustup to version ${RUST_VERSION}"
rustup update ${RUST_VERSION}
sudo ln -s /root/.cargo/bin/rustc /usr/bin/rustc
sudo ln -s /root/.cargo/bin/cargo /usr/bin/cargo
echo "Setting executable permissions for rustc and cargo"
sudo chmod +x /usr/bin/rustc
sudo chmod +x /usr/bin/cargo

echo "Installing python-miio from git master branch"
python${PYTHON_VERSION:0:3} -m ensurepip
python${PYTHON_VERSION:0:3} -m pip install --upgrade pip
python${PYTHON_VERSION:0:3} -m pip install git+https://github.com/rytilahti/python-miio.git@master

