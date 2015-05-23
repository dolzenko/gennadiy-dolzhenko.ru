require 'erb'

desc "Generate work.html from work.erb.html"
task :generate_works_list do
  File.write('work.html', ERB.new(File.read('work.erb.html')).result)
end

desc "Generate paper.html from paper.erb.html"
task :generate_papers_list do
  File.write('paper.html', ERB.new(File.read('paper.erb.html')).result)
end

desc "Generate work pages using work_item_template.erb.html and works.rb"
task :generate_work_pages => :replace_menu_fragments do
  work_item_template = ERB.new(File.read('work_item_template.erb.html'))

  for work in eval(File.read('works.rb').strip)
    res = work_item_template.result(binding)
    f = File.new('work\\' + work[:id].to_s + '_res.html', 'w')
    f.write(res)
    f.close()
  end
end

desc "Regenrate menu in *.html files with do_menu.rb"
task :replace_menu_fragments do
  sh 'ruby do_menu.rb'
end

desc "Build site"
task :build => [:replace_menu_fragments, :generate_works_list, :generate_work_pages] do
end

desc "Deploy site"
task :deploy => :build do
  sh 'gsutil -m rsync -r . gs://www.gennadiy-dolzhenko.ru'
end

task :default => :build
