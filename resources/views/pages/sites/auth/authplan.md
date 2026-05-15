I want to have a login and registration page on my site.

But because it's just a portfolio and people don't want to make an account for just that, I want it to be a lot simpeler.

I want people to just fill in their own, or a xxx@lookatme.com fake email.

They then can get a generated password of a random password generator (real words and numbers, not a random string) ( and that is hashed in the db), but then after they can also create a passkey.

People that then want to reach out to me for instance can use their account, basically turning my site into a mini LinkdIn too (maybe for future site ideas).

Onboarding and account creating is easy,

Just, display name, gender and a remember token (just follow what the user field in the database says.)

For gender, it's male, female, non-binary or other, or prefer not to say.
Males get assigned the pfpmale.png
Females the pfpfemale.png
and others get pfpnb.png
as profilepicture for in-site stuff.

The files are found in public/assets/general/

From there, there needs to be a admin panel for assigned admins (only me basically) but that is defined by the role field in the users table.
The admin panel and all admin sites can only be viewed by admin roles.

I don't know if you can change the core database, but edit the users table like you see fit.

On the admin page, create the for-now-empty tabs:
- Users (you will be able to see all and delete all users)
- Sites (see all sites, link to those sites, see all subpages)
- Site info (subpage of the sites tab where you can see and edit all metadata)
- Site create (We will spend some time here, basically the entire process of creating all those files will be automated.)