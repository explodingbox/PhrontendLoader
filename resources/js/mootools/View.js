VIEWCLASSNAME = new Class({
  
  initialize: function(params){


  },

  draw: function(){
    this.element = renderView('VIEWTITLE');

    this.attachEvents();
    return this.element;
  },

  scrape: function(element){
    this.element = element;

  },

  attachEvents: function(){


  }


});