// pages/mbti/progress.js
const app = getApp();
Page({
  /**
   * 页面的初始数据
   */
  data: {
    topic: "",
  },

  chooseAnswer: function (event) {
    var topicAnswer = event.currentTarget.dataset.value;
    var answerGuid = getApp().globalData.answerGuid;
    var topicNumber = getApp().globalData.currentTopicNumber;
    this.saveAnswer(answerGuid, topicNumber,topicAnswer);
  },

  saveAnswer: function (answerGuid,topicNumber,topicAnswer) {
    var that = this;
    wx.request({
      url: app.globalData.thirdServerBaseUrl + '/game/index/saveAnswer',
      data: { topicNumber: topicNumber, topicAnswer: topicAnswer, answerGuid: answerGuid },
      success:function(){
        var nextTopicNumber = topicNumber + 1;
        getApp().globalData.currentTopicNumber = nextTopicNumber;
        that.loadTopic(nextTopicNumber);
      },
    });
  },

  loadTopic: function (topicNumber=0) {
    var that = this;
    if(topicNumber==0){
      topicNumber = getApp().globalData.currentTopicNumber;
    }

    wx.request({
      url: app.globalData.thirdServerBaseUrl + '/game/index/gettopic4client',
      data: { topicNumber: topicNumber},
      header: {//请求头
        "Content-Type": "applciation/json"
      },
      method: "GET",
      success: function (res) {
        that.setData({
          topic: JSON.parse(res.data),
        });        
      },
    });
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.loadTopic();
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

  }
})