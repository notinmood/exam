const util = require('../../utils/util.js');
var app = getApp();

// pages/mbti/index.js
Page({
  /**
   * 页面的初始数据
   */
  data: {
    existUnfinished: false,
    unfinishedAnswerGuid: '',
  },

  startNewAnswer: function() {
    var answerGuid = util.genGuid();
    getApp().globalData.answerGuid = answerGuid;
    var userGuid = getApp().globalData.userOpenID;
    this.beginAnswer4Server(answerGuid, userGuid);
  },

  continueExistAnswer: function() {

  },

  beginAnswer4Server: function(answerGuid, userGuid) {
    var that = this;
    wx.request({
      url: app.globalData.thirdServerBaseUrl + '/game/index/beginAnswer4Client',
      data: {
        answerGuid: answerGuid,
        userGuid: userGuid
      },
      success: function(data, textStatus) {
        that.navNextStep();
      },
    });
  },

  navNextStep: function() {
    wx.navigateTo({
      url: 'progress',
      success: function(res) {},
      fail: function(res) {},
      complete: function(res) {},
    });
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    var that = this;
    var userGuid = app.globalData.userOpenID;
    if(userGuid){}else{
      app.userGuidCallback= ug=>{
        userGuid= ug;
      }
    }

    wx.request({
      url: app.globalData.thirdServerBaseUrl + '/game/index/getUnfinishedAnswer4Client',
      data: {
        userGuid: userGuid
      },
      success: function(res) {
        var result = res.data;
        getApp().globalData.temp = result;
        if (result) {
          that.setData({
            existUnfinished: true,
            unfinishedAnswerGuid: result.answerguid
          });
        } else {
          that.setData({
            existUnfinished: false
          });
        }
      },
    })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function() {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function() {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function() {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function() {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function() {

  }
})