function clickText(id) {

    if (id == "searchType1") {
        var id = document.getElementById(id);
        if (id.value == "발행년도") {
            document.getElementById("searchWord1").value = "발행년도 4자리 입력";
        } else {
            document.getElementById("searchWord1").value = "";
        }
    } else if (id == "searchType2") {
        var id = document.getElementById(id);
        if (id.value == "발행년도") {
            document.getElementById("searchWord2").value = "발행년도 4자리 입력";
        } else {
            document.getElementById("searchWord2").value = "";
        }
    } else {
        var id = document.getElementById(id);
        if (id.value == "발행년도") {
            document.getElementById("searchWord3").value = "발행년도 4자리 입력";
        } else {
            document.getElementById("searchWord3").value = "";
        }
    }
}