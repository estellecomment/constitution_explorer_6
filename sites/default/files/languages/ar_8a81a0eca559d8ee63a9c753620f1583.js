Drupal.locale = { 'pluralFormula': function($n) { return Number((($n==1)?(0):(($n==0)?(1):(($n==2)?(2):(((($n%100)>=3)&&(($n%100)<=10))?(3):(((($n%100)>=11)&&(($n%100)<=99))?(4):5)))))); }, 'strings': { "The changes to these blocks will not be saved until the \x3cem\x3eSave blocks\x3c/em\x3e button is clicked.": "لن تُحفظ التغييرات في هذه الصناديق قبل النقر على زر \x3cem\x3eاحفظ الصناديق\x3c/em\x3e." } };