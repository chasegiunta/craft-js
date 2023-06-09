import { test, expect } from "vitest";
import CraftJs, { _Craft } from "./../src/web/assets/craft/src/craft";

test("CraftQuery", () => {
  const craft = CraftJs("http://next.test");
  expect(craft.apiUrl).toEqual("http://next.test");
  expect(craft.endpoint).toEqual("/actions/craft-js/craft");
});

test("entries", () => {
  const craft = CraftJs("http://next.test");
  const entriesQuery = craft.entries();
  expect(entriesQuery.filters.entries).toEqual(true);
});

test("section", () => {
  const craft = CraftJs("http://next.test");
  const teamsQuery = craft.entries().section("teams");
  expect(teamsQuery.filters.section).toEqual("teams");
});

test("Undefined Methods", () => {
  const craft = CraftJs("http://next.test");
  const teamsQuery = craft.entries().section("teams").division("d1");
  expect(teamsQuery.filters.division).toEqual("d1");
});

test("Teams Listing Query", async () => {
  const craft = CraftJs("http://next.test");
  let teamsQuery = craft
    .entries()
    .section("teams")
    .division("d1")
    .limit(20)
    .prune(["title", "url", "address (state)", "conference"]);
  expect(teamsQuery.filters.section).toEqual("teams");
  expect(teamsQuery.filters.division).toEqual("d1");
  expect(teamsQuery.filters.limit).toEqual(20);
  expect(teamsQuery.filters.prune).toEqual(
    JSON.stringify(["title", "url", "address (state)", "conference"])
  );

  // set division to null
  teamsQuery = teamsQuery.division(null);
  expect(teamsQuery.filters.division).toEqual(null);

  await teamsQuery.fetch().then((response) => {
    expect(response.data).toHaveLength(20);
    expect(response.data).toHaveProperty("0.title");
    expect(response.data).not.toHaveProperty("0.id");
  });
});

test("batch", async () => {
  const craft = CraftJs("http://next.test");

  const query1 = craft
    .entries()
    .section("teams")
    .division("d1")
    .limit(20)
    .prune(["title"]);
  const query2 = craft.entries().section("news").limit(5).prune(["title"]);

  await craft
    .batch([query1, query2])
    .fetch()
    .then((responses: any[]) => {
      const teams = responses[0];
      const news = responses[1];

      expect(teams.data).toHaveLength(20);
      expect(news.data).toHaveLength(5);
    });
});
