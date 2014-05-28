# Description:
#   Get todays meals from restarant Karavan
#
# Dependencies:
#   "moment-timezone": "0.0.3"
#   "underscore": "1.6.0"
#
# Configuration:
#   HUBOT_KARAVAN_URL
#
# Commands:
#   hubot lunch - Return the list of todays lunches
#
# Author:
#   Denocle

moment = require 'moment-timezone'
_ = require 'underscore'

module.exports = (robot) ->
  unless process.env.HUBOT_KARAVAN_URL?
    robot.logger.warning 'The environment variable HUBOT_KARAVAN_URL is not set.'
    return

  robot.respond /lunch/i, (msg) ->
    lunch msg

lunch = (msg) ->
  strApiUrl = process.env.HUBOT_KARAVAN_URL

  unless strApiUrl?
    msg.send 'The environment variable HUBOT_KARAVAN_URL is not set.'
    return

  msg.send 'Getting todays lunch, hold on...'
  msg.http(strApiUrl).get() (err, res, body) ->

    json = JSON.parse(body)

    if json isnt null
      arrMeals = []
      for day in json
        if day.day is moment().format('dddd')
          for meal in day.meals
            arrMeals.push _.unescape meal

      strResponse = arrMeals.join "\n"
    else
      strResponse = 'Could not fetch lunch: ' + err

    msg.send strResponse
