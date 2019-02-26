
            var j,c,x;
            var focused;
            function init(){
                for (j = 0; j < i.length; j++) {
                      var btn = document.createElement("BUTTON");
                      btn.value = i[j];
                      var t = document.createTextNode(i[j]);
                      btn.appendChild(t);
                      btn.addEventListener('onclick', makeClickHandler(btn));
                      btn.style.borderRadius = "4px";
                      btn.style.cursor = "pointer";
                      document.getElementById('keyboard').appendChild(btn);
                }

                // Make the space button separately.
                var btn = document.createElement("BUTTON");
                btn.value = ' ';
                btn.id = 'space';
                var t = document.createTextNode("Space");
                btn.appendChild(t);
                btn.addEventListener('onclick', makeClickHandler(btn));
                btn.style.borderRadius = "4px";
                btn.style.cursor = "pointer";
                document.getElementById('keyboard').appendChild(btn);


                // Save which of the text inputs was the last focused, write to this one
                // when the keyboard is used.
                j = document.getElementsByTagName('input');
                for (x = 0; x < j.length; x++) {
                    if (j[x].type === 'text') {
                        j[x].onblur = function() {
                            focused = this;
                        };
                    }
                }
            }

            // Create typing function
            function makeClickHandler(c) {
                c.onclick=function() {
                    focused.value += this.value.toLowerCase();
                    focused.focus();
                };
            }

            window.addEventListener?
                window.addEventListener('load',init,false):
                window.attachEvent('onload',init);
        })();
    </script>

</head>
</div><!-- end #keyboard -->
</html>
