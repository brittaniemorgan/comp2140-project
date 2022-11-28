window.onload = function(){
    class RequestManager{
        constructor(){}
        
        sendData(url){
            let request = new XMLHttpRequest();
            request.onreadystatechange = function(){
            if (request.readyState === XMLHttpRequest.DONE){
                if (request.status === 200){
                    return request.responseText;
                }}
            }
            request.open("GET", url);
            request.send(); 
            
        }

        getFoodDetails(foodID){
            var url = "http://localhost/comp2140-project/index.php?foodID=" + foodID;
            this.sendData(url);
        }
    }
    var reqManager = new RequestManager();

    function openPopUp() {
        console.log("woii"); 
        /*var foodID = document.querySelector(".itemID").innerHTML ;
        console.log(foodID);
        //document.querySelector("#food-description").innerHTML = "";
        reqManager.getFoodDetails(foodID);*/
        var overlay = document.querySelector("#overLay");
        overlay.style.display = "block";
    }

    function closePopUp(){
        var overlay = document.querySelector("#overLay");
        overlay.style.display = "none";
    }


    var foodButtons = document.getElementsByClassName("addToOrderButton");
    for (var i = 0; i < foodButtons.length ; i++){
        foodButtons[i].addEventListener("click",openPopUp);
    }
}