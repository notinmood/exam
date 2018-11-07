//app.js
App({
  onLaunch: function () {
    var that = this;
    // 展示本地存储能力
    var logs = wx.getStorageSync('logs') || []
    logs.unshift(Date.now())
    wx.setStorageSync('logs', logs);

    // 登录
    wx.login({
      success: res => {
        // 发送 res.code 到后台换取 openId, sessionKey, unionId
        console.log("code:"+res.code);
        var that = this;
        wx.request({
          url: that.globalData.thirdServerBaseUrl +'/game/api/getopenid',
          data:{code:res.code},

          success:function(re){
            var openid= (re.data);
            that.globalData.userOpenID = openid;
            if (that.userGuidCallback) {
              that.userGuidCallback(openid);
              console.log('pppp:'+openid);
            }
          },
        });
      }
    })
    // 获取用户信息
    wx.getSetting({
      success: res => {
        if (res.authSetting['scope.userInfo']) {
          // 已经授权，可以直接调用 getUserInfo 获取头像昵称，不会弹框
          wx.getUserInfo({
            success: res => {
              // 可以将 res 发送给后台解码出 unionId
              this.globalData.userInfo = res.userInfo

              // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
              // 所以此处加入 callback 以防止这种情况
              if (this.userInfoReadyCallback) {
                this.userInfoReadyCallback(res)
              }
            }
          })
        }
      }
    })
  },
  globalData: {
    userInfo: null,
    thirdServerBaseUrl:"https://app.rainytop.com/exam/index.php",
    /**小程序使用者的openid */
    userOpenID:'',
    answerGuid:'',
    currentTopicNumber:1,

    appid: '1wqas2342dasaqwe2323424ac23qwe',//appid
    secret: 'e0dassdadef2424234209bwqqweqw123ccqwa',//secret

    temp:null,
  }
})