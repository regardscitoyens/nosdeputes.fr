export PYENV_ROOT="$HOME/.pyenv"
export PATH="$PYENV_ROOT/bin:$PATH"    # if `pyenv` is not already on PATH
eval "$(pyenv init --path)"
eval "$(pyenv init -)"
eval "$(pyenv virtualenv-init -)"
export PYENV_VIRTUALENV_DISABLE_PROMPT=1
pyenv activate nosdeputes27

# Install VENV with:
# curl -L https://github.com/pyenv/pyenv-installer/raw/master/bin/pyenv-installer | bash
# export PYENV_ROOT="$HOME/.pyenv"
# export PATH="$PYENV_ROOT/bin:$PATH"
# eval "$(pyenv init --path)"
# eval "$(pyenv init -)"
# eval "$(pyenv virtualenv-init -)"
# export PYENV_VIRTUALENV_DISABLE_PROMPT=1
# pyenv install 2.7.18
# This might fail, in which case, first install python dependencies as advised here: https://github.com/pyenv/pyenv/wiki#suggested-build-environment and there: https://github.com/pyenv/pyenv/wiki/Common-build-problems
# pyenv virtualenv 2.7.18 nosdeputes27
# pyenv activate nosdeputes27
# pip install bs4 requests lxml
