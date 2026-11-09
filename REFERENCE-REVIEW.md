# Reference review

Reviewed 9 November 2026. Public JobServe pages were reviewed without signing in. The original PHP attachments were used as functional references and are excluded from this project.

## JobServe

| Public page reviewed | Observed or publicly documented | SignalHire implementation |
| --- | --- | --- |
| [UK job search](https://www.jobserve.com/gb/en/job-search/) | The public form exposes keyword, title, location and job type fields, plus remote and pay filters. | Public results filter by keyword, location and permanent or contract. Location search also matches work arrangement. Details show work arrangement and GBP salary or day rate. |
| [Job Seeker Registration](https://www.jobserve.com/gb/en/Candidate/Registration.aspx) and [Upload your CV](https://www.jobserve.com/gb/en/Candidate/CandidateQuickPostResume.aspx) | Public forms show account creation and CV upload fields. The upload page lists a 2 MB limit and several document formats. | Separate candidate and recruiter roles, a candidate-managed profile, and private PDF or plain text CV storage. |
| [Job Seeker FAQs](https://jobserve.com/gb/en/content/help/faqs/candidate.htm?showAns=mobileApps) and [mobile help](https://www.jobserve.com/gb/en/mob/Content/Help) | Public guidance describes applying from the advert, attaching or selecting a CV, and tracking applications from the signed-in account. | Sign in from the job details page, upload or use the current CV, submit once, and track the outcome in My Applications. |
| [Post a Job](https://recruiter.jobserve.com/gb/en/Recruiter/JobPostAdd.aspx) and [Job Manager help](https://www.jobserve.com/gb/en/content/help/advert-manager.htm) | The indexed post form describes configurable application methods. Job Manager help documents amending live adverts and expiring them to remove them from results. | A company-owned workspace with draft, publish, edit and close actions. Applications stay within SignalHire. |
| [Advertiser FAQs](https://jobserve.com/gb/en/content/help/faqs/advertiser.htm) | Public guidance describes viewing, downloading and resending received applications through Job Manager. | View applicants for owned adverts, download the submitted CV, and update the candidate-visible status. |
| [Profile Help](https://www.jobserve.com/gb/en/content/help-for-my-profile.htm) and [CV Search Help](https://recruiter.jobserve.com/gb/en/content/help/cvsearch.htm) | Public documentation describes profiles derived from searchable CVs and a separate recruiter CV search feature. | Candidates edit their profiles manually. Recruiters access CVs only through applications to their own vacancies. |

Authenticated screens and live submissions were outside this public review. The help pages document candidate-side tracking and recruiter-side access to applications. They do not establish that a recruiter’s status change automatically appears in the candidate’s JobServe account. That behaviour and the explicit draft lifecycle are SignalHire design decisions. The Post a Job form was available as indexed content. A direct retrieval of the recruiter-hosted form returned an error.

## Attached PHP

| File | Workflow | What the supplied code shows and how the MVP handles it |
| --- | --- | --- |
| `find_jobs(1).php` | Find jobs | Loads slices of recent active adverts and fetches company data inside the result loop. SignalHire joins company data and applies keyword, location and employment type filters. |
| `details_page(1).php` | Job details and apply | Reads the requested ID into SQL, shows a hard-coded CV name and links to confirmation endpoints absent from the attachments. SignalHire uses bound IDs, the candidate’s current CV and a validated POST. |
| `complete_full_profile(1).php` | Candidate onboarding | Loads the candidate profile and offers a multipart profile/CV form. Its save handler is absent. SignalHire supplies separate profile save and CV upload actions. |
| `edit_cv(1).php` | Replace candidate CV | Provides a file field and posts to a separate handler absent from the attachments. SignalHire validates the replacement and retains previously submitted versions. |
| `file_upload_parser(1).php` | Media upload | Handles video, uses embedded database credentials and a path based on the client extension and timestamp. The size check sits in the missing-file branch. SignalHire uses private, validated CV storage and no embedded database secrets. |
| `edit_rec_reg(1).php` | Recruiter account details | Edits an existing account via an absent handler and renders the stored password field back into the page. Recruiter registration itself is outside the supplied file. SignalHire creates accounts with hashed passwords and keeps company editing separate. |
| `company_profile(1).php` | Company overview | Prefills overview, logo and benefits fields from a recruiter record. The authentication guard is commented out and the save handler is absent. SignalHire uses an owner-scoped company profile. |
| `company_profile_1(1).php` | About the company | Presents mission, HQ and social inputs with repeated field names and no visible save form. SignalHire stores the company name, location and description in one form. |
| `company_profile_2(1).php` | Employee benefits | Shows benefits controls and posts to a thank-you page. Persistent benefit editing is absent from the supplied file. SignalHire uses the company description for the core overview. |
| `edit_rec_job(1).php` | Edit an advert | Checks for a recruiter session, then reads the advert by ID without a recruiter ownership condition. The update handler is absent. SignalHire scopes every recruiter edit to the company owner. |
| `dashboard(1).php` | Candidate dashboard | Displays candidate documents and profile sections with external edit links and GET-based deletion links. Application tracking is absent. SignalHire supplies My Profile and My Applications. |
| `cv_search(1).php` | Recruiter CV search | Lists recent active candidate records and loads related video data. No complete search workflow or explicit access check appears in this file. The expanded release adds opt-in profile discovery with no CV downloads from the directory. |
| `cv_details_page(1).php` | Candidate/CV details | Queries a candidate record using the request ID directly and displays candidate information. The file does not establish access through an owned application. SignalHire authorises the application before retrieving its submitted CV. |

The attachments depend on external connection, navigation and save handlers. They contain removed `mysql_*` APIs, interpolated queries, raw output, inconsistent session checks and error messages that can expose database details. SignalHire replaces these with PDO prepared statements, HTML escaping, role and ownership checks, CSRF-protected mutations, hashed passwords and generic HTTP error pages.
