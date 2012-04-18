require 'jscat'
require 'filestojs'
require 'yaml'

class FrontendLoader

  attr_accessor :resources_path

  def initialize
    version = '0.0.1'
    @resources_path = Gem.path[0]+"/gems/frontendloader-"+version+"/resources"
  end

  def init_app    
    config_path = @resources_path+"/FrontendLoader.yml"
    %x[cp #{config_path} FrontendLoader.yml]
    %x[cp #{@resources_path}/js/helpers/mustache.js mustache.js]
    %x[cp #{@resources_path}/js/mootools/mootools.js mootools.js]
    %x[cp #{@resources_path}/js/mootools/renderView.js renderView.js]
    %x[cp #{@resources_path}/less/reset.less reset.less]
    puts "Created basic FrontendLoader app"
  end

  def load_settings
    if File.exists? 'FrontendLoader.yml'
      @settings = YAML.load_file('FrontendLoader.yml')
      return true
    else
      puts 'Run fel init first yo'
      return false
    end
  end
  
  def compile
    return false unless load_settings
    
    #less
    
    less_files = Dir.glob("*.less")

    if less_files.length > 0 then

      less_files.delete("style.less")
      less_string = ""
      less_files.each { |file|
        file = file.gsub(".less","")
        less_string = less_string + "@import '#{file}'; \n"
        File.open('style.less','w') { |f| 
           f.write(less_string)
         }
      }
      %x[lessc style.less style.css]
      %x[rm style.less]
      
    end
    
    
    #TEMPLATES

    template_joiner = FilesToJs.new({
      :file_dir        => '.',
      :file_format     => @settings['templates']['format'],
      :js_object_name  => @settings['templates']['varname'],
      :output          => @settings['templates']['varname']+".js",
    })
    template_joiner.write_js
    
    
    
    #Javascript (includes joined templates)
    

    javascript = JsCat.new({
      :js_dir => '.',
      :prioritize => @settings['javascript']['prioritize'],
      :ignore => ['js.js'],
      :compress => @settings['javascript']['compress'],
      :output => 'js.js'
    })
    
    %x[rm templates.js]
    
    puts "Compiled into js.js and style.css"
    
  end
  
  
  def create_view(view_title)
    return false unless load_settings
        
    style_format = @settings['css']['framework']
    style_directory = @settings['css']['directory']
    style_string = ".#{view_title} {

    }
    "
    
    if File.exists? "#{view_title}.#{@settings['css']['framework']}" then
      puts "View already exists"
      return false
    end
    
    
    File.open("#{style_directory}/#{view_title}.less",'w') { |f| 
      f.write(style_string)
    }
    
    classwords = view_title.split('_')
    classwords.each {|word|
      word.capitalize!
    }
    
    classname = classwords.join
    
    if classwords.last != "View" then classname = classname+"View" end
    
    js_directory = @settings['javascript']['directory']
    
    js_string = ""
    
    viewtemplate =  File.open("#{@resources_path}/js/mootools/View.js",'r').read
    js_string = js_string+viewtemplate.gsub('VIEWCLASSNAME',classname).gsub('VIEWTITLE',view_title)


    File.open("#{js_directory}/#{classname}.js",'w') { |f| 
      f.write(js_string)
    }

    templates_directory = @settings['templates']['directory']
    templates_format = @settings['templates']['format']

    File.open("#{templates_directory}/#{view_title}.#{templates_format}",'w') { |f| 
      f.write("")
    }
    

  end
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  

  
end