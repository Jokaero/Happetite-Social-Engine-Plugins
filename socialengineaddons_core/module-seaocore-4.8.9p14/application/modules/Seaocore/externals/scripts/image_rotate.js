  var durationOfRotateImage = 5000;
  function image_rotate() {
    var slideshowDivObj = $('slide-images');
    var imagesObj = slideshowDivObj.getElements('img');
    var indexOfRotation = 0;

    imagesObj.each(function(img, i){
      if(i > 0) {
        img.set('opacity',0);
      }
    });    

    var show = function() {
      imagesObj[indexOfRotation].fade('out');
      indexOfRotation = indexOfRotation < imagesObj.length - 1 ? indexOfRotation+1 : 0;
      imagesObj[indexOfRotation].fade('in');
    };
    
    window.addEvent('load',function(){
      show.periodical(durationOfRotateImage);
    });
  }