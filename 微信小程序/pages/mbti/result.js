import * as echarts from '../../ec-canvas/echarts';

let chart = null;

function initChart(canvas, width, height) {
  chart = echarts.init(canvas, null, {
    width: width,
    height: height
  });
  canvas.setChart(chart);

  var option = {
    title: {
      text: '性格类型各指标得分情况',
      subtext: '',
      x: 'center'
    },
    color: ['#37a2da', '#32c5e9', '#67e0e3'],
    tooltip: {
      trigger: 'axis',
      axisPointer: {            // 坐标轴指示器，坐标轴触发有效
        type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
      },
      show: false
    },
    xAxis: [
      {
        type: 'value',
        //将X坐标轴上的负值转换为正值显示
        axisLabel: {
          formatter: function (value, index) {
            if (value < 0) {
              return -value;
            } else {
              return value;
            }
          }
        }
      }
    ],
    yAxis: [
      {
        type: 'category',
        axisTick: { show: true },
        data: ['E', 'S', 'T', 'J']
      },
      {
        type: 'category',
        axisTick: { show: true },
        data: ['I', 'N', 'F', 'P']
      }
    ],
    series: [
      {
        name: '前',
        type: 'bar',
        stack: '总量',
        label: {
          normal: {
            show: true,
            position: 'right',
          },
        },
        data: [15,17,22,19]
      },
      {
        name: '后',
        type: 'bar',
        stack: '总量',
        label: {
          normal: {
            show: true,
            position: 'left',
            formatter: function (data) {
              if (data.value < 0) {
                return -data.value;
              } else {
                return data.value;
              }
            }
          }
        },
        data: [-20,-11,-15,-18]
      }
    ]
  };

  chart.setOption(option);
  return chart;
}


Page({

  /**
   * 页面的初始数据
   */
  data: {
    answerGuid:'',
    ec: {
      onInit: initChart
    }
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    if (options.answerGuid){
      var answerGuid = options.answerGuid;
      this.setData({ answerGuid: answerGuid });
    }else{
      this.setData({ answerGuid: '测试数据' });
    }
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