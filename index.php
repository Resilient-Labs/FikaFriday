<?php
    include 'dbStuff.php';
?>
<html>

<head>
    <link rel="stylesheet" href="fika.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
</head>

<body>
    <div class="container" id="text-container">
        <h1>Fika Friday</h1>
        <h2>For Friday, <span id="nextFriday"></span></h2>
        <h2>This week's prompt</h2>
        <textarea id="textarea" placeholder="What should we talk about this week?"></textarea>
    </div>

     <div class="container" id="history-container">
        <h2>Last Week's Pairs</h2>
        <textarea id="history-area" placeholder="Paste last week's pairs here"></textarea>
    </div>

    <div class="container" id="pairs-container">
        <div id="splash">
            <h2>This Week's Participants</h2>
            <div class='square' id='add_user'><p></p> Add Employee </div>
            <button onclick="pairM()"> Make Pairs </button>
        </div>
        <div class='' id='splash2'>
            <h2>Confirm Pairs</h2>
            <span id="pairs"></span>
            <div id="buttons">
                <button onclick="goBack()" id="back">Back</button>
                <button onclick="slack()" id="send">Send</button>
            </div>
        </div>

    </div>

    <script>
        var dummy = [], oddNumber = false;
        //What's the next Friday?
        var today = new Date();
        var resultDate = new Date(today.getTime());
        var monthNames = ["January", "February", "March", "April", "May", "June","July", "August", "September", "October", "November", "December"];
        //Use 5 for Friday
        resultDate.setDate(today.getDate() + (7 + 5 - today.getDay()) % 7);
        document.getElementById("nextFriday").innerHTML = monthNames[resultDate.getMonth()] + " " + resultDate.getDate();
        var getFullName = function(employee){
            return "" + employee.first + " " + employee.last;
        }


        //Adding User
        var add_u = document.getElementById("add_user");
        add_u.addEventListener("click", function () {
            var new_user = {
                first: prompt('Enter First Name'),
                last: prompt('Enter Last Name'),
                selected: true,
            }

            if (new_user.first != null && new_user.last != null){

            //Pass this new user to the database
            $.ajax({ url: 'dbStuff.php',
                 data: {action: 'addUser', newFirst:new_user.first, newLast:new_user.last},
                 type: 'post',
                 success: function(output) {
                              console.log("sucessfully added");
                          }
            });
            <?php
//                addEntry(new_user.first, new_user.last);
            ?>
            employee.push(new_user);
            drawE_square(new_user, "splash");
            }

        });

        var employee = [
            <?php
                //Grab all of the entries in the database
                while($row = mysqli_fetch_assoc($results)){
            ?>
            {   
                //Creates new "employee" for each enry
                "first":<?php echo json_encode($row["first"]); ?>,
                "last":<?php echo json_encode($row["last"]); ?>,
                "selected":true
            },
            <?php	
                // end the loop and free up memory
                }
                mysqli_free_result($results);
            ?>
        ];
        
        var pairX = [];
        //Added this to determine whether or not to add elements to splash or splash2
        var drawE_square = function (object, container) {
            var element = document.getElementById("" + container),
                square = document.createElement("div");
            square.className = ("square");
            square.addEventListener("click",
                function fade(drawE_square) {
                    for (i = 0; i < employee.length; i++) {
                        var name = employee[i].first + " " + employee[i].last
                        if (name === square.children[0].innerHTML) {
                            //check to see if the square is faded
                            if (square.style.opacity == 0.5) {
                                square.style.opacity = 1;
                                employee[i].selected = true;
                                console.log("This Should Be Unfaded");
                            } else {
                                employee[i].selected = false;
                                square.style.opacity = 0.5;
                                console.log("This square should be Fading");
                            }
                            console.log(employee[i]);
                        }
                    }
                }
            );
            var first = document.createElement('p');
            var second = document.createTextNode(object.first + ' ' + object.last);
            first.appendChild(second);
            square.appendChild(first);
            
         
            var edit_link = document.createElement('a');
            var edit_text = document.createTextNode('Edit');
            edit_link.appendChild(edit_text);
            square.appendChild(edit_link);

            //EDITING USER
            edit_link.addEventListener("click", function (event) {

                event.stopPropagation();
                var oldFirst = object.first; 
                var oldLast = object.last;
                object.first = prompt('Enter First Name');
                object.last = prompt('Enter Last Name');
                if (!!object.first && !!object.last){
                    second = document.createTextNode(object.first + ' ' + object.last);
                    first.innerHTML = "";
                    first.appendChild(second);

                    $.ajax({ url: 'dbStuff.php',
                 data: {action: 'editUser', newFirst:object.first, newLast:object.last, oldName:oldName},
                 type: 'post',
                 success: function(output) {
                              console.log("sucessfully edited");
                          }
                });
                }
                else {
                    second = document.createTextNode(oldFirst + ' ' + oldLast);
                    first.innerHTML = "";
                    first.appendChild(second);
                }
                
            });
            //REMOVING USER
            var remove_link = document.createElement('a');
            var remove_text = document.createTextNode('Remove');
            remove_link.appendChild(remove_text);
            square.appendChild(remove_link);

            remove_link.addEventListener("click", function (event) {
                console.log(second);
                if (confirm("You sure you want to delete " + second.nodeValue + " forever?")) {
                    for (i = 0; i < employee.length; i++) {
                        var name = employee[i].first + " " + employee[i].last;
                        if (name === square.children[0].innerHTML) {
                            $.ajax({ url: 'dbStuff.php',
                                 data: {action: 'removeUser', formerEmployee: employee[i].last},
                                 type: 'post',
                                 success: function(output) {
                                            console.log("sucessfully removed");
                                            employee.splice(i, 1);
                                          }
                                }); 
                        }
                    }
                    element.removeChild(square);

                } else {
                    event.stopPropagation();
                }

            });
            //Added this to determine whether or not to add things to splash or splash2
            element.appendChild(square);

        };
        //PAIRING FUNCTIONALITY
        function pairM() {
                oddNumber = false;
                dummy = [];
                //MAKING SURE NO PAIRS HAVE BEEN MADE
                pairX = [];
                 
                for (i = 0; i < employee.length; i++) {
                    if (employee[i].selected == true) {
                        dummy.push(employee[i]);
                    }
                }
                var group = dummy.length;
                //Checks to see if there are an even number of Employees
                if (dummy.length % 2 > 0) {
                    oddNumber = true;
                    group --;
                }   

                    for (i = 0; i < group / 2;) {
                        console.log("i is " + i);
                        var random = Math.round(Math.random() * (dummy.length - 2)) + 1,
                            pairHistory = document.getElementById("history-area").value.split(/\n| /),
                            numHistoryPairs = pairHistory.length/3,
                            freshPair = true;
                            console.log("random number is  " + random);
                            console.log(dummy.length);
                        //MAKE SURE PAIRS FROM LAST WEEK DON'T GET REPEATED
                        //ASSUMING HISTORY.LENGTH % 3 = 0
                        for(j = 0; j < numHistoryPairs; j++){
                            //LOOP THROUGH THE HISTORY ARRAY
                          
                            console.log(dummy[0].first + " and " + dummy[random].first + " compared to " + pairHistory[0] + " and " + pairHistory[2] + " is: " + (dummy[0].first == pairHistory[0] && dummy[random].first == pairHistory[2]));
                            if((dummy[0].first == pairHistory[0] && dummy[random].first == pairHistory[2]) || (dummy[0].first == pairHistory[2] && dummy[random].first == pairHistory[0])){
                                console.log("stale pair alert");
                                freshPair = false;
                            }
                            // console.log("j is " + j)
                            pairHistory.splice(0,3);
                        }
                        if(freshPair == true){
                            var pairS = [ dummy[0], dummy[random] ];
                            dummy.splice(random, 1);
                            dummy.splice(0, 1);
                            pairX.push(pairS);;
                            i++;
                        }else{
                            //CHECK TO SEE IF THIS IS THE LAST PAIR
                            if(dummy.length === 2 ){
                                console.log("this is the last pair, so lets try this again!");
                                pairM();
                            }else{
                                //STALE PAIR - GET NEW RANDOM NUMBER
                                console.log("getting a new random number cause there was a stale pair");
                            }
                            
                        }
                    };
                    console.log("This week's pairs are: ");
                    console.log(pairX);
                    //Empty the pairs span
                    document.getElementById("pairs").innerHTML = "";
                    //Loop through pairX, and draw each square (drawE_square)
                    for (i = 0; i < pairX.length; i++) {
                        var grow = 0;
                        drawE_square(pairX[i][grow], "pairs");
                        grow++;
                        drawE_square(pairX[i][grow], "pairs");
                    }
                    if(oddNumber){
                        drawE_square(dummy[0], "pairs");
                    }
                    //Move splash1 over
                    var splash1 = document.getElementById("splash");
                    splash1.className += splash1.className ? ' moved' : 'moved';
                    //Move splash2 over
                    var splash2 = document.getElementById("splash2");
                    splash2.className += splash2.className ? ' moved' : 'moved';

            }
        //Draws all the squares
        for (i = 0; i < employee.length; i++) {
            drawE_square(employee[i], "splash");
        }
        var goBack = function () {
                var splash1 = document.getElementById("splash");
                splash1.className = "";
                //Move splash2 over
                var splash2 = document.getElementById("splash2");
                splash2.className = "";
                //Empty the array of pairs so they don't get redrawn
                pairX = [];
            }
            //Slack Integration
        var slack = function () {

            var message = document.getElementById("textarea").value;
            var slackURL = <?php echo json_encode($slackURL) ?>;
            var slackMesage = "This week's Fika Friday Message is: " + message + ".\n Here are the pairs: ";
            for (i = 0; i < pairX.length; i++) {
                slackMesage += "\n" + pairX[i][0].first + " and " + pairX[i][1].first;
            }
            if (oddNumber) {
                slackMesage += " and " + dummy[0].first;
            }
            $.ajax({
                type: 'POST',
                url: slackURL ,
                data: JSON.stringify({
                    "text": slackMesage
                }),
                async: false,
                //     dataType: 'json',
                success: function (data) {
                    alert("This week's pairs have been sent out!");
                },
                error: function (t, e) {
                    console.log("You're so close!! " + e);
                },
                processData: false

            });
        };
    </script>

</body>

</html>
<?php
    //CLOSE DATABASE CONNECTION
	mysqli_close($connection);
?>
