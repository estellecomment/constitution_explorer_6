/* $Id: glossary.js,v 1.1.4.3 2008/06/29 18:25:55 nancyw Exp $ */

function glossary_replace_handler(event) {
  // Disable superscript field if not selected.
  if ($("input[@name=glossary_replace]:checked").val() == 'superscript') {
    $("input[@name=glossary_superscript]").parents("div.glossary_superscript").show();
  }
  else {
    $("input[@name=glossary_superscript]").parents("div.glossary_superscript").hide();
  }

  // Disable icon URL field if not selected.
  if ($("input[@name=glossary_replace]:checked").val() == 'icon') {
    $("input[@name=glossary_icon]").parents("div.glossary_icon").show();
  }
  else {
    $("input[@name=glossary_icon]").parents("div.glossary_icon").hide();;
  }
}

function glossary_link_related_handler(event) {
  // Disable one-way field if not selected.
  if ($("input[@name=glossary_link_related]:checked").val() == 1) {
    $("input[@name=glossary_link_related_how]").parents("div.glossary_link_related_how").show();
  }
  else {
    $("input[@name=glossary_link_related_how]").val(0);
    $("input[@name=glossary_link_related_how]").parents("div.glossary_link_related_how").hide();
  }
}

// Run the javascript on page load.
if (Drupal.jsEnabled) {
  $(document).ready(function () {
  // On page load, determine the current settings.
  glossary_replace_handler();
  glossary_link_related_handler();

  // Bind the functions to click events.
  $("input[@name=glossary_replace]").bind("click", glossary_replace_handler);
  $("input[@name=glossary_link_related]").bind("click", glossary_link_related_handler);
  });
}



// ESTELLE - code from freemind's html, for folding/unfolding the glossary page.
// START FOLDING

// Here we implement folding. It works fine with MSIE5.5, MSIE6.0 and
// Mozilla 0.9.6.

if (document.layers) {

//Netscape 4 specific code

pre = 'document.';

post = ''; }

if (document.getElementById) {

//Netscape 6 specific code

pre = 'document.getElementById("';

post = '").style'; }

if (document.all) {

//IE4+ specific code

pre = 'document.all.';

post = '.style'; }



function layer_exists(layer) {

try {

eval(pre + layer + post);

return true; }

catch (error) {

return false; }}



function show_layer(layer) {

eval(pre + layer + post).position = 'relative';

eval(pre + layer + post).visibility = 'visible'; }



function hide_layer(layer) {

var truc = pre + layer + post;

eval(pre + layer + post).visibility = 'hidden';

eval(pre + layer + post).position = 'absolute'; }



function hide_folder(folder) {

hide_folding_layer(folder)

show_layer('show'+folder);



scrollBy(0,0); // This is a work around to make it work in Browsers (Explorer, Mozilla)

}



function show_folder(folder) {

// Precondition: all subfolders are folded



show_layer('hide'+folder);

hide_layer('show'+folder);

show_layer('fold'+folder);



scrollBy(0,0); // This is a work around to make it work in Browsers (Explorer, Mozilla)



var i;

for (i=1; layer_exists('fold'+folder+'_'+i); ++i) {

show_layer('show'+folder+'_'+i); }

}

function show_folder_completely(folder) {

// Precondition: all subfolders are folded



show_layer('hide'+folder);

hide_layer('show'+folder);

show_layer('fold'+folder);



scrollBy(0,0); // This is a work around to make it work in Browsers (Explorer, Mozilla)



var i;

for (i=1; layer_exists('fold'+folder+'_'+i); ++i) {

show_folder_completely(folder+'_'+i); }

}







function hide_folding_layer(folder) {

var i;

for (i=1; layer_exists('fold'+folder+'_'+i); ++i) {

hide_folding_layer(folder+'_'+i); }



hide_layer('hide'+folder);

hide_layer('show'+folder);

hide_layer('fold'+folder);



scrollBy(0,0); // This is a work around to make it work in Browsers (Explorer, Mozilla)

}



function fold_document() {

var i;

var folder = '1';

for (i=1; layer_exists('fold'+folder+'_'+i); ++i) {

hide_folder(folder+'_'+i); }

}



function unfold_document() {

var i;

var folder = '1';

for (i=1; layer_exists('fold'+folder+'_'+i); ++i) {

show_folder_completely(folder+'_'+i); }

}

// END FOLDING 