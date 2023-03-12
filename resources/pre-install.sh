#!/bin/bash 

RUST_VERSION="1.48.0" 
PYTHON_VERSION="3.8" 

echo "Lancement de l'installation des dépendances de Dreame"

if ! python${PYTHON_VERSION} -c "import sys; assert sys.version_info >= (3, 8, 2)" >/dev/null 2>&1; then
    echo "Python ${PYTHON_VERSION} ou une version ultérieure n'est pas installé. Installation en cours..."
    sudo apt-get update -q
    sudo apt-get install -y curl build-essential libffi-dev libssl-dev zlib1g-dev libbz2-dev libreadline-dev libsqlite3-dev wget llvm libncurses5-dev xz-utils tk-dev libxml2-dev libxmlsec1-dev libffi-dev liblzma-dev
    curl -O https://www.python.org/ftp/python/${PYTHON_VERSION}/Python-${PYTHON_VERSION}.tar.xz
    tar -xf Python-${PYTHON_VERSION}.tar.xz
    cd Python-${PYTHON_VERSION}
    echo "Lancement de './configure'"
    ./configure --enable-optimizations
    echo "Lancement de 'make'"
    make -j 4
    echo "Lancement de 'make altinstall'"
    sudo make altinstall
fi

if ! [ -x "$(command -v rustup)" ]; then
    echo "Installation de rustup"
    curl --proto '=https' --tlsv1.2 -sSf https://sh.rustup.rs | sh -s -- -y
fi

echo "Mise à jour de rustup vers la version ${RUST_VERSION}"
rustup update ${RUST_VERSION}

sudo ln -sf /root/.cargo/bin/rustc /usr/bin/rustc 
sudo ln -sf /root/.cargo/bin/cargo /usr/bin/cargo 

echo "Attribution des permissions d'exécution pour rustc et cargo"
sudo chmod +x /usr/bin/rustc 
sudo chmod +x /usr/bin/cargo

echo "Installation de python-miio depuis la branche master de git"
python${PYTHON_VERSION} -m ensurepip
python${PYTHON_VERSION} -m pip install --upgrade pip
python${PYTHON_VERSION} -m pip install git+https://github.com/rytilahti/python-miio.git@master
