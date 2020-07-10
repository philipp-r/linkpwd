$('textarea').on("input", function(){
  var currentLength = $(this).val().length;
	document.getElementById("charCounter").innerHTML = currentLength;
});
