jQuery( function( $ ) {
  // 表示/非表示
  $('.display-switch').on('click', function() {
  	var status = $('.display', this).text();
  	if (status == '非表示にする') {
  	  $(this).parent().find('table').hide();
  	  $(this).parent().find('.graph').hide();
  	  $('.display', this).text('表示する');
    } else {
  	  $(this).parent().find('table').show();
  	  $(this).parent().find('.graph').show();
  	  $('.display', this).text('非表示にする');
    }
  });













































  });
  $('#linkFactory').on('click', function(){
  	location.href = '/search/Factofy';
  });
  $('#linkBusinessType').on('click', function(){
  	location.href = '/compare/MajorBusinessType';
  });
  $('#linkTransporter').on('click', function(){
  	location.href = '/compare/CompanyDivision';
  });
  $('#linkPref').on('click', function(){
  	location.href = '/compare/Pref';
  });
  $('#linkGas').on('click', function(){
  	location.href = '/compare/Gas';
  });
  $('#linkMenu').on('click', function(){




  });


});