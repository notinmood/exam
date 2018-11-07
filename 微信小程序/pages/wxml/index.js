// pages/wxml/index.js
const app = getApp();

Page({
  /**
   * 页面的初始数据
   */
  data: {
      time:(new Date()).toString(),
      myarray:[
        {
          city:'qingdao',
        },
        {
          city:'beijing',
        },
        {
          city:'shanghai',
        },
      ],
    mymessage: "",
    information:"",
    postbackinfo:"",   
  },

  clickme:function(event){
    //this.information= "非常完美";
    this.setData({information:"very good",});
  },

 

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.getdata();

  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  },

  postdata:function(){
    var that = this; 
    wx.request({
      header:{"content-type":"application/x-www-form-urlencoded"},
      url: app.globalData.thirdServerBaseUrl + '/game/api/postdata',//请求地址
      data:{id:35,name:"zhangsan"},
      success:function(res){
        that.setData({postbackinfo:res.data});
      },
    })
  },  

  getdata: function () {//定义函数名称
    var that = this;   // 这个地方非常重要，重置data{}里数据时候setData方法的this应为以及函数的this, 如果在下方的sucess直接写this就变成了wx.request()的this了
    wx.request({
      url: app.globalData.thirdServerBaseUrl+ '/game/api',//请求地址
      data: {//发送给后台的数据
        
      },
      header: {//请求头
        "Content-Type": "applciation/json"
      },
      method: "GET",//get为默认方法/POST
      success: function (res) {
        console.log(res.data);//res.data相当于ajax里面的data,为后台返回的数据
        　　　　　　that.setData({//如果在sucess直接写this就变成了wx.request()的this了.必须为getdata函数的this,不然无法重置调用函数

          　　　　　　mymessage: res.data

        　　　　　　　　　　})

      },
      fail: function (err) { },//请求失败
      complete: function () { }//请求完成后执行的函数
    })
  },
})