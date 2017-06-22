//--------------------------- hide filter-------------------------------------//

$('#filter').on("click", function (event)
{
  if(!$('#options').hasClass('hidden')){
    // console.log('francis');
    $('#options').addClass('hidden');
  } else {
      // console.log('coucou');
      $('#options').removeClass('hidden');

  }
})
$('#erase-all').change(function(){
  // console.log($('.category-box').val)
  $('.category-box').prop('checked', false);
})
