export interface EntryQuery extends ElementQuery {
  [x: string]: any;
  section(section: string): EntryQuery;
  sectionId(value: number | "not" | number[]): EntryQuery;
  type(value: string | EntryType): EntryQuery;
  typeId(value: number | "not" | number[]): EntryQuery;
  authorId(value: number | "not" | number[]): EntryQuery;
  authorGroup(value: string | UserGroup | UserGroup[] | null): EntryQuery;
  authorGroupId(value: number | "not" | number[]): EntryQuery;
  postDate(value: string | string[]): EntryQuery;
  before(value: string | Date): EntryQuery;
  after(value: string | Date): EntryQuery;
  expiryDate(value: string | string[]): EntryQuery;
  status(value: string | string[]): EntryQuery;
}

interface Section {
  id: number;
  structureId?: number;
  type: string;
}

interface EntryType {
  id: number;
}

interface UserGroup {
  id: number;
}

export interface ElementQuery {
  [x: string]: any;
  limit(limit: number): ElementQuery;
  orderBy(order: string): ElementQuery;
  paginate(page: number): ElementQuery;
  prune(items: string[]): ElementQuery;

  one(): Promise<any>;
  all(): Promise<any>;
  exists(): Promise<any>;
  ids(): Promise<any>;
  count(): Promise<any>;
}

export interface Craft {
  [x: string]: any;
  batch(queries: Craft[]): Craft;
  entries(): EntryQuery;
  // assets(): AssetQuery;
  // users(): UserQuery;
  // Add other initial methods if there are any
}
