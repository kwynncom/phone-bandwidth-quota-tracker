function byid(id) { return document.getElementById(id); }

var KW_PQUO_INPUTS = false;

window.onload = function() {
    var inps = document.querySelectorAll('input');
    KW_PQUO_INPUTS = inps;
    inps.forEach(function(inp) { inp.oninput = delayInput; });
}

var KW_PQUO_TOV = false;
function delayInput() {
    if (this.id === 'quota') byid('ausage').max = this.value * 1024;
    if (KW_PQUO_TOV) clearTimeout(KW_PQUO_TOV);
    KW_PQUO_TOV = setTimeout(function() { myOnInput(); }, 600);
}

function myOnInput() {

    const inps = KW_PQUO_INPUTS;
    let q = '';
    
    inps.forEach(function(inp) {
	if (!inp.validity.valid) return;
	q +=  inp.id;
	q += '=' + inp.value;
	q += '&';
    });

    const xhr = new XMLHttpRequest(); 
    xhr.open('GET', 'index.php?' + q, true);
    xhr.onreadystatechange = function() { if (this.readyState === 4 && this.status === 200) byid('calcs').innerHTML = this.response; }
    xhr.send();
}

