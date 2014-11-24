<!-- Previous page link -->
<% if(pages > 1){
    if(page > 1) { %>
    <a href="#" data-page="<%=prev%>">&laquo;</a> |
<%}%>
<!-- Numbered page links -->
<% for ( var i = 0; i < pageRange.length; i++ ) { %>
  <% if(page != pageRange[i]) { %>
    <a href="#" data-page="<%=pageRange[i]%>"><%=pageRange[i] %></a>
  <%
    } else{ %>
    <span><%=pageRange[i] %></span>
<%
    }
    if(i < (pageRange.length - 1)){%>
        |
    <%}
  }
%>
<!-- Next page link -->
<% if(page < pages) { %>
    <a href="#" data-page="<%=next%>">&raquo;</a>
<%
    }
}%>