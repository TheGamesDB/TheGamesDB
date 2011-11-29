$(document).ready(function(){
 /* The following code is executed once the DOM is loaded */
 alert("Flippin Eck!");
 
$('.frontCover').bind("click",function(){
 
// $(this) point to the clicked .sponsorFlip element (caching it in elem for speed):
 
var elem = $(this);
 
// data('flipped') is a flag we set when we flip the element:
 
if(elem.data('flipped'))
 {
 // If the element has already been flipped, use the revertFlip method
 // defined by the plug-in to revert to the default state automatically:
 
elem.revertFlip();
 
// Unsetting the flag:
 elem.data('flipped',false)
 }
 else
 {
 // Using the flip method defined by the plugin:
 
elem.flip({
 direction:'lr',
 speed: 350,
 onBefore: function(){

 // Setting the flag:
 elem.data('flipped',true);
 }
 });
 
});