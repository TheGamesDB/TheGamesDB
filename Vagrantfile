# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "ubuntu/trusty64"

  config.vm.network "forwarded_port", guest: 80, host: 8888

  config.vm.provision "shell", inline: <<-SH
    set -e -x
    aptitude update
    aptitude install -R -y ansible
    ansible-playbook -i localhost, -c local /var/www/html/ansible/playbook.yml
  SH

  config.vm.synced_folder ".", "/var/www/html", owner: "root", group: "www-data"
end
