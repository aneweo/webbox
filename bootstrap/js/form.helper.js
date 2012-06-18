// Toggle checkboxes
function selectToggle(toggle, form) {
     var myForm = document.forms[form];
     for( var i=0; i < myForm.length; i++ ) { 
          if(toggle) {
               myForm.elements[i].checked = "checked";
          } 
          else {
               myForm.elements[i].checked = "";
          }
     }
}
// Check checkboxes before submitting
function check(form) {
    count = 0;
    var myForm = document.forms[form];
	for( var x=0; x < myForm.length; x++){
		if(myForm.elements[x].checked){
            count++;
		}
	}
	
	if(count==0){
		alert("Please select an item");
        return false;
	}
    return true;
}
// Prevent enter key from submitting the form
function disableEnterKey(e) {
     var key;

     if(window.event)
          key = window.event.keyCode;     //IE
     else
          key = e.which;     //firefox

     if(key == 13)
          return false;
     else
          return true;
}
