// $Id: webauth.js 1239 2009-02-16 23:25:54Z ksharp $

function inheritAccessFromParent(pNodes, sNode) {

	var nodes = pNodes.split(",");

	if (nodes.length < 1) return false;

	var pList = document.getElementById("edit-book-bid");
	var pIndex = 0;

	if (pList == null) {
		return false;
	} else {
		pIndex = pList.selectedIndex;
		if (pIndex < 0) return false;
	}

	var bid = pList.options[pIndex].value;

	if (bid == 0 || bid == 'new' || (sNode != null && bid == sNode)) {
		return false;
	}

	// see if this top-level node is in our list of nodes from which a child
	// inherits access rights.
	for (i=0; i < nodes.length; i++) {
		if (nodes[i] == bid)  {
			return true; 
		}
	}

	return false;
}

function setDivDisplay(divID,style) {
	var mydiv = document.getElementById(divID);
	if (mydiv != null) {
		mydiv.style.display=style;
	}
}

function toggleCheckbox(elID,check,disable) {

	var myEl = document.getElementById(elID);
	if (myEl != null) {
		if (check != null) {
			myEl.checked = check;
		}
		if (disable != null) {
			myEl.disabled = disable;
		}
	}
}

function overClick(check) {
	if (check == null) check = document.getElementById("edit-fldOverride-1").checked;
	if (check) {
		setDivDisplay('waHidem', 'inline');
		
	} else {
		setDivDisplay('waHidem', 'none');
	
	}

}

function parentClick(pNodes, pNodeID) {

        var pList = document.getElementById("edit-book-bid");
        if (pList != null) {
		var nodeInherits = false;
		if (pList.selectedIndex > 0) {
			nodeInherits = inheritAccessFromParent(pNodes,pNodeID);
		}

		if (nodeInherits) {

			setDivDisplay('waNodeInherits','inline');
			toggleCheckbox('edit-fldInheritsFromTop-1',true,true);
			toggleCheckbox('edit-fldInherit-1',false,true);
			setDivDisplay('waInherit','none');
			setDivDisplay('waRoles','none');
			setDivDisplay('waGroups','none');
			setDivDisplay('waDefault','none');
			setDivDisplay('waRestrict', 'none');

		} else {

			toggleCheckbox('edit-fldInheritsFromTop-1',false,true);
			setDivDisplay('waNodeInherits','none');
			setDivDisplay('waInherit','inline');
			var bid = pList.options[pList.selectedIndex].value;
			if (bid == 0 || bid == 'new' ) {
				toggleCheckbox('edit-fldInherit-1',true,false);
			} else if (pNodeID != null && bid == pNodeID) {
                                var myEl = document.getElementById('edit-fldInherit-1');
				var checkit = true;
                                if (myEl != null) {
			                checkit = myEl.checked;
                        	}
				toggleCheckbox('edit-fldInherit-1',checkit, false);
			} else {
				toggleCheckbox('edit-fldInherit-1',false,true);
			}
			setDivDisplay('waRoles','inline');
			setDivDisplay('waGroups','inline');
			setDivDisplay('waDefault','inline');
			setDivDisplay('waRestrict','inline');
						
		}
	}			
}

