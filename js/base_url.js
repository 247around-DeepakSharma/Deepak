/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 var getUrl = window.location;

 //Un-comment below line for localhost
 var baseUrl = getUrl .protocol + "//" + getUrl.host  + "/" + getUrl.pathname.split('/')[1];

 //Comment below line for localhost, this is for main server
 //var baseUrl = getUrl .protocol + "//" + getUrl.host ;


