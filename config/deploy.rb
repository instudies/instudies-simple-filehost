set :application, 			"Instudies"
set :domain,				"upload.dev.instudi.es"
set :deploy_to, 			"/var/www/#{domain}"
default_run_options[:pty] = true

set :repository,			"git@github.com:instudies/instudies-simple-filehost.git"
set :branch,				"master"
set :scm,					:git
set :deploy_via,			:copy
set :keep_releases, 3

server "#{domain}", 		:app, :web, :db, :primary => true
set :ssh_options, 			{:forward_agent => true, :port => 22}
set :user,					"deployer"
set :use_sudo,				false

role :web,					domain
role :app,					domain
role :db,					domain, :primary => true

set :shared_children,		[
								"vendor",
								"config",
								"log"
							]

namespace :instudies do

    task :vendors do
        run "curl -s http://getcomposer.org/installer | php -- --install-dir=#{release_path}"
        run "cd #{release_path} && #{release_path}/composer.phar install"
		run "sudo chown -R nginx:www-workers #{release_path} #{release_path}/../../current"
		run "sudo chmod -R 670 #{release_path} #{release_path}/../../current"
    end

end

after "deploy:update_code", "instudies:vendors"