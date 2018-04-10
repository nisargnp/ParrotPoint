// RUN `php websocket_server/bin/server.php` in the background 
// before opening these test files in XAMPP

/*
TODO:
- implement professor session check in the DB on the WSS
- separate professor and student view for this page

- LATER: make a url to query a pdf via a name

*/

var defaultPDFUrl = "/389NGroupProject/static/pdf/introHTML.pdf";

var pdfDoc = null;
var conn = null;

var pageRendering = false;
var pageNumPending = null;
var incPage = decPage = gotoLocal = gotoMaster = setMasterPage = null;
var numPages = 0;

var scale = 1.5;
var canvas = document.getElementById("pdf_view");
var context = canvas.getContext('2d');

var isProfessor = false;

var savedChat = "";

// PDF Functions

function makePageManager(curr) {
    let master = parseInt(curr);
    let local = master;

    let inc = function() {
        // how to get max page
        if (local < numPages) {
            local += 1;
            queueRenderPage(local);
        }
    };

    let dec = function() {
        if (local > 1) {
            local -= 1;
            queueRenderPage(local);
        }
    };

    let gotoLocal = function() {
        queueRenderPage(local);
    }

    let gotoMaster = function() {
        local = master;
        queueRenderPage(master);
    }

    let setMaster = function(newMaster) {
        console.log("Got new master page");
        master = parseInt(newMaster);
    }    

    return [inc, dec, gotoLocal, gotoMaster, setMaster];
}

function generatePDF(pdfPage) {
    if (pdfDoc != null) {
        pageRendering = true;

        pdfDoc.getPage(pdfPage).then(
            function(page) {
                var viewport = page.getViewport(scale);
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                var renderContext = {
                    canvasContext: context,
                    viewport: viewport
                }

                var renderTask = page.render(renderContext);

                // Wait for rendering to finish
                renderTask.promise.then(function() {
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        // New page rendering is pending
                        generatePDF(pageNumPending);
                        pageNumPending = null;
                    }
                });

                // set input element's new value
                $("#pdfNumber").val(pdfPage);

                if (isProfessor) {
                    // update page num if u are professor
                    conn.send(pdfPage);
                }

            });
    }
}

function queueRenderPage(n) {
    let num = parseInt(n);

    if (num < 1)
        return;

    // set page number label
    let pnl = document.getElementById("page-num");
    pnl.innerText = num;

    if (pageRendering) {
        pageNumPending = num;
    } else {
        generatePDF(num);
    }
}

// Chat functions

function updateScroll() {
    let element = document.getElementById("chat-box");
    element.scrollTop = element.scrollHeight;
}


var addToChatBox = function(msg, sender = "YOU") {
    let cbl = document.getElementById("chat-box-list");
    let nc = document.createElement("li");

    let s = document.createElement("b");
    s.appendChild(document.createTextNode(sender));

    let text = document.createTextNode(": " + msg);

    nc.appendChild(s);
    nc.appendChild(text);
    cbl.appendChild(nc);

    updateScroll();
}

var sendMessage = function() {
    let inputBox = document.getElementById("chat-text-box");
    let msg = inputBox.value;

    if (msg != "") {
        // add to chat box
        addToChatBox(msg);

        // send message
        conn.send("chat:" + msg);

        // reset input
        inputBox.value = "";

        return false;
    }
}

// call this right when page loads to set user name given form other page
function setChatName(name) {
    // assuming input has been sanitized by somewhere else
    conn.send("set-name:" + name);
}

// professor stuff

function setProfessor() {
    // after login prof starts a php session
    // when prof starts a lecture send session_id() to wss to be stored
        // map session id to random code generated
    // here, we will send session_id() + lecture code to be validated
    // prevents students from sending a similar message and becoming professor
    
    // but we need other pages to be made b4 this. so for now lets just send this
    conn.send("auth-professor:1");
    isProfessor = true;
}

$(function() {
    // Initially download PDF
    PDFJS.getDocument(defaultPDFUrl).then(function(pdfDoc_) {
        pdfDoc = pdfDoc_;

        numPages = pdfDoc.pdfInfo.numPages;

        // Setup Web Socket
        conn = new WebSocket('ws://localhost:3001');
        conn.onopen = function(e) {
            console.log("Connection established!");
        };

        conn.onmessage = function(e) {

            console.log(e.data);

            let [header] = e.data.split(":", 1);

            if (header == "chat") {
                let [_, sender, message] = e.data.split(":", 3);
                addToChatBox(message, sender);
            } 

            // polling
            else if (header == "polling") {

                let [_, action, info] = e.data.split(":", 3);
                console.log(e.data.split(":", 3));

                // polling start
                if (action == "start") {
                    
                    // save and disable chat
                    savedChat = document.getElementById("chat-box").innerHTML;
                    document.getElementById("chat-text-box").disabled = true;
                    document.getElementById("chat-send-btn").disabled = false;

                    // update send button
                    document.getElementById("chat-send-btn").setAttribute("onclick", "sendPoll(); return false;");

                    // replace chat w/ poll
                    numAnswers = parseInt(info);

                    document.getElementById("chat-box").innerHTML = "<h3>Select Answer:</h3>";
                    let form = document.createElement("form");
                    for (let i = 0; i < numAnswers; i++) {

                        let space3 = "&nbsp;&nbsp;&nbsp";

                        let label = document.createElement("label");
                        label.innerHTML = "<strong>" + space3 + i + "</strong>"; 

                        let radio = document.createElement("input");
                        radio.setAttribute("type", "radio");
                        radio.setAttribute("name", "radio-poll");
                        radio.setAttribute("value", "" + i);
                        if (i == 0) {
                            radio.setAttribute("checked", "checked");
                        }

                        form.appendChild(radio);
                        form.appendChild(label);
                        form.appendChild(document.createElement("br"));

                    }

                    document.getElementById("chat-box").appendChild(form);
                    
                }

                // polling stop
                else if (action == "stop") {

                    console.log("restoring chat: " + savedChat);

                    // restore chat
                    if (savedChat.length > 0) {
                        document.getElementById("chat-box").innerHTML = savedChat;
                    }

                    // restore send button
                    document.getElementById("chat-send-btn").disabled = false;
                    document.getElementById("chat-send-btn").setAttribute("onclick", "sendMessage(); return false;");

                    // enable chat
                    document.getElementById("chat-text-box").disabled = false;

                }

                // existing poll -> client cannot join
                else if (action == "active") {
                    savedChat = document.getElementById("chat-box").innerHTML;
                    deactivateChatAfterPoll("Poll currently active.");
                }

                // polling results
                else if (action == "results") {

                    console.log("results:");
                    console.log(info);

                    let results = JSON.parse(info);

                    let labels = [];
                    for (let i = 0; i < results.length; i++) {
                        labels[i] = "" + i;
                    }

                    createAndDisplayGraph(results, labels);

                }

            }

            else {
                if (!isNaN(e.data)) {
                    let pageNum = parseInt(e.data);

                    if (gotoMaster == null) {
                        [incPage, decPage, gotoLocal, gotoMaster, setMasterPage] = makePageManager(pageNum);
                        queueRenderPage(pageNum);
                    }
                    else {
                        setMasterPage(pageNum);
                    }
                    console.log(e.data);
                }
            }
        };
    });

    // correct scroll for chat box
    updateScroll();

    // set chat textbox / button handlers
    document.getElementById("chat-text-box").addEventListener("keydown", function(e) {
        if (e.keyCode == 13 && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
            return false;
        }
    });

});

var startPolling = function() {
    console.log("start polling");
    document.getElementById("polling").textContent = "Stop Polling";
    document.getElementById("polling").onclick = stopPolling;
    conn.send("polling-start");
}

var stopPolling = function() {
    console.log("stop polling");
    document.getElementById("polling").textContent = "Start Polling";
    document.getElementById("polling").onclick = startPolling;
    conn.send("polling-stop");
}

var graphClose = function() {
    document.getElementById('graph-popup').style.display = "none";
};

var sendPoll = function() {

    let numChoices = $("input[name=radio-poll]").length;
    let choice = parseInt($("input[name=radio-poll]:checked").val());

    results = []
    for (let i = 0; i < numChoices; i++) {
        results[i] = i == choice ? 1 : 0;
    }

    console.log("Sending poll results:");
    console.log(results);

    conn.send("polling-reply:" + JSON.stringify(results));

    deactivateChatAfterPoll("Sent!");

}

function deactivateChatAfterPoll(msg = "") {

    document.getElementById("chat-text-box").disabled = true;
    document.getElementById("chat-send-btn").disabled = true;

    if (msg.length > 0) {
        msg += "<br><br>";
    }

    let strEle = document.createElement("strong");
    strEle.innerHTML = msg + "Chat disabled until polling is over.<br>";
    
    document.getElementById("chat-box").innerText = "";
    document.getElementById("chat-box").appendChild(strEle);

}

function createAndDisplayGraph(results, labels) {
    
    // create canvas
    document.getElementById("popup-canvas-div").innerHTML = '<canvas id="popup-canvas"></canvas>';

    // get ref to canvas
    let ctx = document.getElementById("popup-canvas").getContext('2d');

    // crate graph
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: results,
                backgroundColor: [
                    'rgba(255, 0, 0, 0.2)',
                    'rgba(0, 255, 0, 0.2)',
                    'rgba(0, 0, 255, 0.2)',
                    'rgba(0, 0, 0, 0.2)',
                ],
                borderColor: [
                    'rgba(255, 0, 0, 0.2)',
                    'rgba(0, 255, 0, 0.2)',
                    'rgba(0, 0, 255, 0.2)',
                    'rgba(0, 0, 0, 0.2)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                position: "top",
                text: "Polling Results",
                fontSize: 20,
                fontColor: "#aaa"
            },
            legend: {
                display: false
            },
            scales: {
              yAxes: [{
                scaleLabel: {
                  display: true,
                  labelString: 'Number of Students'
                },
                ticks: {
                    beginAtZero: true,
                    callback: function(value) {if (value % 1 === 0) {return value;}}
                }
              }],
              xAxes: [{
                scaleLabel: {
                  display: true,
                  labelString: 'Questions'
                }
              }]
            }     
          }
          
    });

    // display graph popup
    document.getElementById('graph-popup').style.display = "block";
}

