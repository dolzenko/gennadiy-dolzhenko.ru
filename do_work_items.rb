require 'erb'

require 'workhash.rb'

f = File.new('work_item_template.erb.html')
t = f.read()

erb = ERB.new(t)

for curw in $work 
	res = erb.result()
	f = File.new('work\\' + curw[:id].to_s + '_res.html', 'w')
	f.write(res)
	f.close()
end