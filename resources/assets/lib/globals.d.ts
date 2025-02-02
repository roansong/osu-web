// interfaces for using process.env
interface Process {
  env: ProcessEnv;
}

interface ProcessEnv {
  [key: string]: string | undefined;
}

declare var process: Process;

declare var window: Window;

// libraries
declare var laroute: any;
// TODO: Turbolinks 5.3 is Typescript, so this should be updated then.
declare var Turbolinks: TurbolinksStatic;

// our helpers
declare var tooltipDefault: TooltipDefault;
declare var osu: OsuCommon;
declare var currentUser: any;
declare var reactTurbolinks: any;
declare var userVerification: any;

// external (to typescript) classes
declare var BeatmapsetFilter: any;
declare var BeatmapDiscussionHelper: BeatmapDiscussionHelperClass;
declare var LoadingOverlay: any;
declare var Timeout: any;

// Global object types
interface Comment {
  id: number;
}

interface BeatmapDiscussionHelperClass {
  url(options: any, useCurrent?: boolean): string;
}

interface JQueryStatic {
  publish: any;
  subscribe: any;
  unsubscribe: any;
}

interface OsuCommon {
  ajaxError: (xhr: JQueryXHR) => void;
  classWithModifiers: (baseName: string, modifiers?: string[]) => string;
  isClickable: (el: HTMLElement) => boolean;
  jsonClone: (obj: any) => any;
  link: (url: string, text: string, options?: { classNames?: string[]; isRemote?: boolean }) => string;
  linkify: (text: string, newWindow?: boolean) => string;
  parseJson: (id: string, remove?: boolean) => any;
  popup: (message: string, type: string) => void;
  presence: (str?: string | null) => string | null;
  present: (str?: string | null) => boolean;
  promisify: (xhr: JQueryXHR) => Promise<any>;
  timeago: (time?: string) => string;
  trans: (...args: any[]) => string;
  transArray: (array: any[], key?: string) => string;
  transChoice: (key: string, count: number, replacements?: any, locale?: string) => string;
  urlPresence: (url?: string | null) => string;
  uuid: () => string;
  formatNumber(num: number, precision?: number, options?: Intl.NumberFormatOptions, locale?: string): string;
  formatNumber(num: null, precision?: number, options?: Intl.NumberFormatOptions, locale?: string): null;
  isDesktop(): boolean;
  isMobile(): boolean;
  updateQueryString(url: string | null, params: { [key: string]: string | undefined }): string;
}

interface Country {
  code?: string;
  name?: string;
}

interface Cover {
  custom_url?: string;
  id?: string;
  url?: string;
}

interface Score {
  id: string;
  mode: string;
  replay: boolean;
  user_id: number;
}

// TODO: should look at combining with the other User.ts at some point.
interface User {
  avatar_url?: string;
  country?: Country;
  country_code?: string;
  cover: Cover;
  default_group: string;
  id: number;
  is_active: boolean;
  is_bot: boolean;
  is_online: boolean;
  is_supporter: boolean;
  last_visit?: string;
  pm_friends_only: boolean;
  profile_colour?: string;
  support_level?: number;
  unread_pm_count?: number;
  username: string;
}

interface TooltipDefault {
  remove: (el: HTMLElement) => void;
}

interface TurbolinksAction {
  action: 'advance' | 'replace' | 'restore';
}

interface TurbolinksStatic {
  controller: any;
  supported: boolean;

  clearCache(): void;
  setProgressBarDelay(delayInMilliseconds: number): void;
  uuid(): string;
  visit(location: string, options?: TurbolinksAction): void;
}
