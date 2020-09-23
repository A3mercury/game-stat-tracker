# Game Stat Tracker

Since Fall Guys doesn't show you stats for levels and wins. I wanted to create a small app that let's me see how awful I'm actually doing.

## Buckets
- [ ] Global List of Games with Fall Guys.
    - FG will be the only game for now, but having an ever growing list will make it easier to add new games in the future.
- [ ] Track Rounds, Levels, Crowns, unlocked Skins, etc.
- [ ] In-game Achievements.
    - [ ] List all achievements and details.
    - [ ] Indicate which achievements are unlocked by player.
- [ ] Leaderboards.
- [ ] Steam account integration.
    - This should let us see when in-game achievements are unlocked. 
    - https://developer.valvesoftware.com/wiki/Steam_Web_API#GetUserStatsForGame_.28v0002.29
- [ ] List all FG data.
    - https://www.npmjs.com/package/fallguys-api
    - [ ] Pull data from ^ and load into our DB.
    - [ ] Run daily job to pull data.
