var abtest = {
  basePath: '',
  
  trackGoal: function(id, obj) {
    // obj can be a link
    if (obj != undefined) {
      if (obj.href != undefined) {
        // it's a link
        obj.href = abtest.basePath+'/track_goal.php?id='+id+'&type=link&url='+escape(obj.href);
      };
    } else {
      jQuery.get(abtest.basePath+'/track_goal.php?id='+id);
    }
  },
  
  trackVariation: function(id) {
    jQuery.get(abtest.basePath+'/track_variation.php?id='+id);
  }
};