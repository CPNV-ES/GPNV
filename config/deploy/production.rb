set :deploy_to, "/home/thegpnv/#{fetch(:application)}"
set :use_sudo, false
set :laravel_set_acl_paths, false
set :laravel_upload_dotenv_file_on_deploy, false
set :composer_install_flags, '--no-dev --prefer-dist --no-interaction --optimize-autoloader'
set :rvm_map_bins

SSHKit.config.command_map[:composer] = "php -d allow_url_fopen=true #{shared_path.join('composer')}"
SSHKit.config.command_map[:readlink] = "readlink"	#avoid problem with readlink

server "gpnv.mycpnv.ch", user: "thegpnv", roles: %w{app db web},   ssh_options: {
     keys: %w(./config/gpnv_rsa),
     forward_agent: false,
     auth_methods: %w(publickey)
   }


after  'composer:run', "copy_dotenv"
after  'composer:run', "get_version"

#Copy .env in the current release
task :copy_dotenv do
	on roles(:all) do
		execute :cp, "#{shared_path}/.env #{release_path}/.env" 
	end
end

# Get the last git version
task :get_version do
	gitVersion = "Not defined"
		
	if(system('git describe --abbrev=0 --tags')) 
		system("require 'open3'")
		stdin, stdout, stderr = Open3.popen3('git describe --abbrev=0 --tags')
		gitVersion = stdout.readlines.to_s[2..-5]
	end

	on roles(:all) do
		execute  :echo, "#{gitVersion} > ~/gpnv.mycpnv.ch/current/public/version.tag"
	end
end








# server-based syntax
# ======================
# Defines a single server with a list of roles and multiple properties.
# You can define all roles on a single server, or split them:

# server "example.com", user: "deploy", roles: %w{app db web}, my_property: :my_value
# server "example.com", user: "deploy", roles: %w{app web}, other_property: :other_value
# server "db.example.com", user: "deploy", roles: %w{db}



# role-based syntax
# ==================

# Defines a role with one or multiple servers. The primary server in each
# group is considered to be the first unless any hosts have the primary
# property set. Specify the username and a domain or IP for the server.
# Don't use `:all`, it's a meta role.

# role :app, %w{deploy@example.com}, my_property: :my_value
# role :web, %w{user1@primary.com user2@additional.com}, other_property: :other_value
# role :db,  %w{deploy@example.com}



# Configuration
# =============
# You can set any configuration variable like in config/deploy.rb
# These variables are then only loaded and set in this stage.
# For available Capistrano configuration variables see the documentation page.
# http://capistranorb.com/documentation/getting-started/configuration/
# Feel free to add new variables to customise your setup.



# Custom SSH Options
# ==================
# You may pass any option but keep in mind that net/ssh understands a
# limited set of options, consult the Net::SSH documentation.
# http://net-ssh.github.io/net-ssh/classes/Net/SSH.html#method-c-start
#
# Global options
# --------------
#  set :ssh_options, {
#    keys: %w(/home/rlisowski/.ssh/id_rsa),
#    forward_agent: false,
#    auth_methods: %w(password)
#  }
#
# The server-based syntax can be used to override options:
# ------------------------------------
# server "example.com",
#   user: "user_name",
#   roles: %w{web app},
#   ssh_options: {
#     user: "user_name", # overrides user setting above
#     keys: %w(/home/user_name/.ssh/id_rsa),
#     forward_agent: false,
#     auth_methods: %w(publickey password)
#     # password: "please use keys"
#   }
