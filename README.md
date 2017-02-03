# Use WordPress posts as event_schedule

A child theme to implement using post-type=post as an event entry. This is a test for something of my work...

## Require

### Plugin

- [CMB2](https://wordpress.org/plugins-wp/cmb2/)
- [smart Archive Page Remove](https://wordpress.org/plugins-wp/smart-archive-page-remove/) -> trying out

### Theme

- [Twentysixteen](https://wordpress.org/themes/twentysixteen/)


## Spec

### On each posts

- event start date (req)
- event start time (req)
- event end date (req)
- event end time
- registration deadline day
- registration deadline time
- ? what to do when an event spans over separate days. e.g. Feb 1, 10, and 20
- ? what to do when an event spans over a period of days e.g. Feb 1-10

### query

- display in order of event date: cat archive
- display in order of event date: tag archive
- display in order of event date: year/month/day archive
- display in order of event date AND only upcoming on front-page

### Shortcodes
- display x number of upcoming events, with filters for cat, year

### Think!

- wp by default has rss per category
