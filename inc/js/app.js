//--------------------------- hide filter-------------------------------------//

$('#filter').on("click", function (event) {
  if(!$('#options').hasClass('hidden')){
    // console.log('francis');
    $('#options').addClass('hidden');
  } else {
      // console.log('coucou');
      $('#options').removeClass('hidden');
  }
})

$('#years').on("click", function (event) {
  if(!$('#options2').hasClass('hidden')){
    // console.log('francis');
    $('#options2').addClass('hidden');
  } else {
      // console.log('coucou');
      $('#options2').removeClass('hidden');
  }
})

$('#popularity').on("click", function (event) {
  if(!$('#options3').hasClass('hidden')){
    // console.log('francis');
    $('#options3').addClass('hidden');
  } else {
      // console.log('coucou');
      $('#options3').removeClass('hidden');
  }
})

$('#erase-all').change(function(){
  // console.log($('.category-box').val)
  $('.category-box').prop('checked', false);
})
