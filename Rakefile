desc "Generate work.html from work.erb.html"
task :generate_works_list => :replace_menu_fragments do
  sh "erb work.erb.html > work.html"
end

desc "Generate work pages using work_item_template.erb.html and works.rb"
task :generate_work_pages => :replace_menu_fragments do
  require 'erb'

  work_item_template = ERB.new(IO.read('work_item_template.erb.html'))

  for work in eval(IO.read('works.rb').strip)
    res = work_item_template.result(binding)
    f = File.new('work\\' + work[:id].to_s + '_res.html', 'w')
    f.write(res)
    f.close()
  end
end

desc "Regenrate menu in *.html files with do_menu.rb"
task :replace_menu_fragments do
  sh 'C:\Ruby19\bin\ruby.exe do_menu.rb'
end

desc "Build site"
task :build => [:replace_menu_fragments, :generate_works_list, :generate_work_pages] do
end

desc "Deploy site"
task :deploy => :build do
  sh 'git status'
  sh 'git push'
  sh 'C:\cygwin\bin\ssh.exe dolzenko@suns.dreamhost.com "cd gennadiy-dolzhenko.ru && git pull"'
end

task :default => :build