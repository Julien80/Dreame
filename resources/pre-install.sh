#!/bin/bash

PYTHON_VERSION="3.8.2"
VENV_DIR=$(pwd)/venv

if [ ! -d "$VENV_DIR" ]; then
    echo "Folder ${VENV_DIR} does NOT exist - EXIT"
    exit 1
fi


echo ""
echo "********************************************"
echo "***** Revert Last Dependencies Install ***** "
echo "********************************************"
sudo python3 -m pip uninstall --yes pip
sudo apt-get -y --reinstall install python3-pip

echo ""
echo ""
echo "***************************************************"
echo "***** Launch pre-install of dreame dependency *****"
echo "***************************************************"

if ! python${PYTHON_VERSION:0:3} -c "import sys; assert sys.version_info >= (3, 8, 2)" >/dev/null 2>&1; then
    echo "Python ${PYTHON_VERSION} or later is not installed. Installing now..."
    sudo apt-get update -q
    sudo apt-get install -y curl build-essential libffi-dev libssl-dev zlib1g-dev libbz2-dev libreadline-dev libsqlite3-dev wget llvm libncurses5-dev xz-utils tk-dev libxml2-dev libxmlsec1-dev libffi-dev liblzma-dev
    curl -O https://www.python.org/ftp/python/${PYTHON_VERSION}/Python-${PYTHON_VERSION}.tar.xz
    tar -xf Python-${PYTHON_VERSION}.tar.xz
    cd Python-${PYTHON_VERSION}
    echo ""
    echo "Running ./configure"
    ./configure --enable-optimizations
    echo ""
    echo "Running make"
    make -j 4
    echo ""
    echo "Running make altinstall"
    sudo make altinstall
else
    echo "Python ${PYTHON_VERSION} already existing"
fi

echo ""
echo ""
echo "*************************"
echo "***** Install VENV ******"
echo "*************************"
sudo apt-get install -y python3 python3-pip python3-venv
sudo python3.8 -m venv $VENV_DIR
sudo $VENV_DIR/bin/python3 -m pip install --upgrade pip wheel

echo ""
echo ""
echo "*********************************************************"
echo "***** Installing python-miio from git master branch *****"
echo "*********************************************************"
sudo apt-get install -y git
sudo $VENV_DIR/bin/python3 -m ensurepip
sudo $VENV_DIR/bin/python3 -m pip install --upgrade pip
sudo $VENV_DIR/bin/python3 -m pip install git+https://github.com/rytilahti/python-miio.git@master
sudo $VENV_DIR/bin/python3 -m pip install --upgrade --force-reinstall pycryptodome
sudo $VENV_DIR/bin/python3 -m pip install micloud
