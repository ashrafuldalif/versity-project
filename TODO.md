<!-- @format -->

# Frontend Fixes - Navbar, Hero, and Club Cards

## Completed Tasks

- [x] Fixed hero section white space by adjusting height to `calc(100vh - 4rem)` to account for fixed navbar
- [x] Updated club cards grid layout to use `minmax(400px, 1fr)` for better sizing on big screens
- [x] Added responsive breakpoint at 1400px to maintain proper card sizing across screen sizes

## Summary of Changes

- **Hero Section**: Changed height from `100vh` to `calc(100vh - 4rem)` to prevent white space below fixed navbar
- **Club Cards**: Increased minimum card width from 350px to 400px, added max-width: 500px constraint, and added a 1400px breakpoint for better proportions on large screens
- **Responsive Design**: Added 1400px breakpoint to ensure cards scale properly across different screen sizes

## Testing Notes

- Hero section should now fill viewport height without white space
- Club cards should appear larger and better proportioned on big screens (1440px+)
- Layout remains responsive on smaller screens
