<html>

<head>
    <script>
        let actualPage = 0;
        let searchTerm = "";
        let lastSearchName = "";
        let lastSearchUsername = "";
        let lastSearchRelevancy = "";
        let lastNames = [];
        let lastUsernames = [];
        let lastRelevancies = [];

        function debounce(func, wait, immediate) {
            var timeout;
            return function() {
                var context = this,
                    args = arguments;
                var later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        };

        function showHint(str) {
            if (str.length == 0) {
                document.getElementById("txtHint").innerHTML = "";
                return;
            } else {
                console.log("Sent request!")
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    console.log("Received request!");

                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("txtHint").innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "gethint.php?q=" + str, true);
                xmlhttp.send();
            }
        }

        function searchWord(str) {
            if (event.keyCode !== 13) {
                return;
            }

            event.preventDefault();

            actualPage = 0;
            lastNames = [];
            lastUsernames = [];
            lastRelevancies = [];

            lastSearchName = "";
            lastSearchUsername = "";
            lastSearchRelevancy = "";

            if (str.length == 0) {
                document.getElementById("txtSearch").innerHTML = "";
                return;
            } else {
                let xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("txtSearch").innerHTML = this.responseText;
                        console.timeEnd("AJAX REQUEST")
                    }
                };
                xmlhttp.open("GET", "getsearch.php?q=" + str, true);
                searchTerm = str;
                console.time("AJAX REQUEST")
                xmlhttp.send();
            }
        }

        function previousPage() {
            actualPage--;

            lastSearchName = lastNames.pop();
            lastSearchUsername = lastUsernames.pop();
            lastSearchRelevancy = lastRelevancies.pop();

            let xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("txtSearch").innerHTML = this.responseText;
                    console.timeEnd("AJAX REQUEST")
                }
            };
            xmlhttp.open("GET", `getsearch.php?q=${searchTerm}&u=${lastSearchUsername}&n=${lastSearchName}&r=${lastSearchRelevancy}`, true);
            console.time("AJAX REQUEST")
            xmlhttp.send();
        }

        function nextPage() {
            actualPage++;

            const names = document.getElementsByClassName('name');
            const name = names.length && names[names.length - 1].textContent;
            const usernames = document.getElementsByClassName('username');
            const username = usernames.length && usernames[usernames.length - 1].textContent;
            const relevancias = document.getElementsByClassName('relevancia');
            const relevancia = relevancias.length && relevancias[relevancias.length - 1].textContent;

            lastNames.push(lastSearchName)
            lastUsernames.push(lastSearchUsername)
            lastRelevancies.push(lastSearchRelevancy)

            lastSearchName = name;
            lastSearchUsername = username;
            lastSearchRelevancy = relevancia;

            let xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("txtSearch").innerHTML = this.responseText;
                    console.timeEnd("AJAX REQUEST")
                }
            };
            xmlhttp.open("GET", `getsearch.php?q=${searchTerm}&u=${username}&n=${name}&r=${relevancia}`, true);
            console.time("AJAX REQUEST")
            xmlhttp.send();
        }

        const debouncedShowHint = debounce(showHint, 200);
    </script>
</head>

<body>
    <div align="center">
        <p>
            <b>
                Digite o nome que você deseja:
            </b>
        </p>
        <form>
            <input type="text" onkeyup="debouncedShowHint(this.value)" onkeypress="searchWord(this.value)">
        </form>
        <b>
            Sugestões:
        </b>
        <p id="txtHint"></p>
    </div>
    <span id="txtSearch"></span>

</body>

</html>