function hrefId(name){
    var patterns = {
        ' ': '-',
        '(': '-',
        ')': '-',
        '/': '-',
        '\\': '-'
    }
    for(var pattern in patterns){
        name = name.replace(new RegExp('\\' + pattern, 'g'), patterns[pattern])
    }
    return name;
}
    
function generateToc(){
    var headings = [], currLen = 0;
    
    $('h2,h3').each(function(){
        var id = hrefId($(this).text());
        $(this).attr('id',id);
        //$(this).before('<a name="' + id + '"></a>')
        if($(this).prop("tagName").toLowerCase() == 'h2'){
            currLen = headings.push({
                heading: $(this),
                children: []
            });                
        }
        else{
            headings[currLen - 1].children.push($(this));
        }
    });
    var html = '<ul class="sidebar-nav nav">';
    for(var i in headings){
        html += '<li><a href="#' + hrefId(headings[i].heading.text()) + '">' + headings[i].heading.text() + '</a>';
        if(headings[i].children.length){
            html += '<ul class="nav">';
            for(var j in headings[i].children){
                html += '<li><a href="' + document.URL + '#' + hrefId(headings[i].children[j].text()) + '">' + headings[i].children[j].text() + '</a></li>';
            }
            html += '</ul>';                
        }
    }
    html += '</ul>';
    $("#apiToc").html(html);
    var sideNavPos = $('#apiToc').offset();
    $('#apiToc').affix({
        offset: {
            top: sideNavPos.top
        }
    });        
    $('body').scrollspy({
        target:'#apiToc'
    });
}


$(document).ready(function(){
    //get headings - generate toc
    generateToc();
});


