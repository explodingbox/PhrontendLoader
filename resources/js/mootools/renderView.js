function renderView(params,m){
  var template;
  var model;
  var template_html;
  var view_element;
  var view_html;
  if (typeOf(params) == 'string') {
    template = params;
    template_html = templates[template];
    model = m || {};
  } else {
    if ( ! params.template ) return false;
    template = params.template;
    model = params.model || {}
    template_html = templates[template];
  }
  view_element = new Element('div.'+template);
  if (params._id) view_element.set('id',params._id);
  view_html = Mustache.to_html(template_html,model,templates);  
  view_element.set('html',view_html);
  return view_element;
};